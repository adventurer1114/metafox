<?php

namespace MetaFox\Mux\Support\Providers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MetaFox\Storage\Models\StorageFile;
use MetaFox\Video\Contracts\VideoServiceInterface;
use MetaFox\Video\Models\Video as Model;
use MetaFox\Video\Support\Facade\Video;
use MuxPhp\Api\AssetsApi;
use MuxPhp\ApiException;
use MuxPhp\Configuration;
use MuxPhp\Models\Asset;
use MuxPhp\Models\CreateAssetRequest;
use MuxPhp\Models\InputSettings;
use MuxPhp\Models\PlaybackPolicy;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Core\Support\FileSystem\Image\Plugins\ResizeImage;
use MetaFox\Core\Support\FileSystem\UploadFile;

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ErrorControlOperator)
 * @todo: Remove dependencies with Video
 */
class Mux implements VideoServiceInterface
{
    private string $clientId;

    private string $clientSecret;

    private string $webHookSecret;

    private string $handler;

    private string $videoPlayback;

    private string $thumbnailPlayback;

    private Configuration $config;

    public const PROVIDER_TYPE                = 'mux';
    public const MUX_WEBHOOK_HEADER           = 'Mux-Signature';
    public const MUX_STREAMING_PATH           = 'https://stream.mux.com';
    public const MUX_IMAGE_PATH               = 'https://image.mux.com';
    public const MUX_WEBHOOK_SETTING_PATH     = 'https://dashboard.mux.com/settings/webhooks';

    public const VIDEO_ASSET_TYPE_READY           = 'video.asset.ready';
    public const VIDEO_ASSET_TYPE_DELETED         = 'video.asset.deleted';
    public const VIDEO_LIVE_STREAM_ACTIVE         = 'video.live_stream.active';
    public const VIDEO_LIVE_STREAM_IDLE           = 'video.live_stream.idle';
    public const VIDEO_LIVE_STREAM_DISABLED       = 'video.live_stream.disabled';
    public const VIDEO_LIVE_STREAM_DELETED        = 'video.live_stream.deleted';
    public const VIDEO_LIVE_STREAM_DISCONNECTED   = 'video.live_stream.disconnected';

    public const BASE_MUX_URL                     = 'https://api.mux.com/video/v1/';

    public const LIVE_SERVER_URL = 'rtmps://global-live.mux.com:443/app';

    public function getProviderType(): string
    {
        return self::PROVIDER_TYPE;
    }

    /**
     * @param string $moduleId
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(string $moduleId = 'video', string $handler = null, bool $throw = true)
    {
        $this->clientId      = Settings::get("mux.$moduleId.client_id", '');
        $this->clientSecret  = Settings::get("mux.$moduleId.client_secret", '');
        $this->webHookSecret = Settings::get("mux.$moduleId.webhook_secret", '');
        $this->handler       = $handler ?? '';
        $this->initPlaybackUrl($moduleId);

        if (!$this->clientId || !$this->clientSecret || !$this->webHookSecret) {
            $throw && abort(500, __p('mux::phrase.missing_service_configs'));
        }

        $this->config = Configuration::getDefaultConfiguration()
            ->setUsername($this->clientId)
            ->setPassword($this->clientSecret);
    }

    /**
     * @param  StorageFile          $file
     * @return array<string, mixed>
     * @throws ApiException
     */
    public function processVideo(StorageFile $file): array
    {
        $url          = app('storage')->getUrl($file->entityId()) ?? '';
        $assetRequest = $this->makeRequest($url);

        $assetsApi = (new AssetsApi(
            new Client(),
            $this->getConfig()
        ))->createAsset($assetRequest);

        $data = $assetsApi->getData();

        if ($data instanceof Asset) {
            return ['asset_id' => $data->getId()];
        }

        return [];
    }

    public function handleWebhook(Request $request): bool
    {
        $verified = $this->verifySignature($request);
        if (!$verified) {
            return false;
        }
        /** @var string $requestContent */
        $requestContent = $request->getContent();

        $body = json_decode($requestContent, true);
        if ($this->handler) {
            try {
                resolve($this->handler)->handleMuxWebhook($body);
            } catch (\Exception $e) {
                abort(500, 'Missing handler method: ' . $e->getMessage());
            }
        } else {
            // todo move to video app
            $data    = collect($body['data']);
            $assetId = $data->get('id');
            $video   = Model::query()->where('asset_id', $assetId)->first();
            switch ($body['type']) {
                case self::VIDEO_ASSET_TYPE_READY:
                    $imagePath   = null;
                    $videoPath   = null;
                    $playbackIds = $data->get('playback_ids');
                    if (!empty($playbackIds)) {
                        $playbackId = $playbackIds[0]['id'];
                        $videoPath  = self::MUX_STREAMING_PATH . DIRECTORY_SEPARATOR . $playbackId . '.m3u8';
                        $imagePath  = self::MUX_IMAGE_PATH . DIRECTORY_SEPARATOR . $playbackId . DIRECTORY_SEPARATOR . 'thumbnail.jpg';
                    }

                    $thumbnail = $this->downloadThumbnail($video, $imagePath);
                    $track     = [];
                    $tracks    = $data->get('tracks');
                    $params    = [
                        'in_process'    => Model::VIDEO_READY,
                        'resolution_x'  => null,
                        'resolution_y'  => null,
                        'duration'      => null,
                        'destination'   => $videoPath,
                        'image_file_id' => $thumbnail instanceof StorageFile ? $thumbnail->entityId() : 0,
                    ];

                    foreach ($tracks as $t) {
                        if ('video' === $t['type']) {
                            $track = $t;
                            break;
                        }
                    }

                    if (isset($track['max_width'])) {
                        $params['resolution_x'] = $track['max_width'];
                    }

                    if (isset($track['max_height'])) {
                        $params['resolution_y'] = $track['max_height'];
                    }

                    if (isset($track['duration'])) {
                        $params['duration'] = $track['duration'];
                    }

                    Model::query()->where('asset_id', $assetId)->update($params);
                    break;
                case self::VIDEO_ASSET_TYPE_DELETED:
                    Video::deleteVideoByAssetId($assetId);
                    break;
                default:
                    return true;
            }
        }

        return true;
    }

    protected function getConfig(): Configuration
    {
        return $this->config;
    }

    protected function makeRequest(string $url, string $policy = PlaybackPolicy::_PUBLIC): CreateAssetRequest
    {
        $input = new InputSettings(['url' => $url]);

        return new CreateAssetRequest([
            'input'           => [$input],
            'playback_policy' => $policy,
        ]);
    }

    protected function verifySignature(Request $request): bool
    {
        $header = $request->header(self::MUX_WEBHOOK_HEADER);
        $body   = $request->getContent();

        if (!is_string($header)) {
            return false;
        }

        $signatureArr = explode(',', $header);
        if (count($signatureArr) < 2) {
            return false;
        }

        $timestamp = Str::replaceFirst('t=', '', $signatureArr[0]);
        $hash      = Str::replaceFirst('v1=', '', $signatureArr[1]);

        $payload = $timestamp . '.' . $body;

        $ourSignature = hash_hmac('sha256', $payload, $this->webHookSecret);

        return hash_equals($ourSignature, $hash);
    }

    protected function downloadThumbnail(?Model $video, ?string $realPath = null): ?StorageFile
    {
        if (!$realPath || !$video) {
            return null;
        }

        $tempFile = sprintf('%s_%s_thumbnail.jpg', tempnam(sys_get_temp_dir(), 'metafox'), Str::uuid());
        file_put_contents($tempFile, file_get_contents($realPath));

        //  remove file before terminating.
        register_shutdown_function(function () use ($tempFile) {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        });

        $uploadedFile = UploadFile::pathToUploadedFile($tempFile);
        if (!$uploadedFile) {
            return null;
        }

        return upload()
            ->setStorage('photo')
            ->setPath('video')
            ->setThumbSizes(ResizeImage::SIZE)
            ->setItemType('photo')
            ->setUser($video->user)
            ->storeFile($uploadedFile);
    }

    public function initPlaybackUrl(string $moduleId): void
    {
        $customVideoPlayback     = Settings::get("$moduleId.custom_video_playback_url");
        $customThumbPlayback     = Settings::get("$moduleId.custom_thumbnail_playback_url");
        $this->videoPlayback     = !empty($customVideoPlayback) ? $customVideoPlayback : self::MUX_STREAMING_PATH;
        $this->thumbnailPlayback = !empty($customThumbPlayback) ? $customThumbPlayback : self::MUX_IMAGE_PATH;
    }

    /**
     * @return string
     */
    public function getVideoPlayback(): string
    {
        return $this->videoPlayback;
    }

    /**
     * @return string
     */
    public function getThumbnailPlayback(): string
    {
        return $this->thumbnailPlayback;
    }

    /**
     * @param  string     $apiName
     * @param  string     $method
     * @param  bool       $returnTransfer
     * @param  string     $postFields
     * @return bool|mixed
     */
    public function executeApi(string $apiName, string $method = 'GET', bool $returnTransfer = false, string $postFields = ''): mixed
    {
        $sUrl = self::BASE_MUX_URL . $apiName;

        if (empty($this->clientId) || empty($this->clientSecret)) {
            return false;
        }
        $curl = curl_init($sUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
        ]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, $returnTransfer);
        curl_setopt($curl, CURLOPT_PUT, $method == 'PUT');
        curl_setopt($curl, CURLOPT_POST, $method == 'POST');
        if (!empty($postFields)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
        }
        $response = curl_exec($curl);
        $err      = curl_error($curl);
        curl_close($curl);

        if (!empty($err) || empty($response)) {
            return false;
        }
        $response = json_decode($response, true);

        if (!empty($response['error'])) {
            return false;
        }

        return $returnTransfer ? $response['data'] : true;
    }

    public function getLiveServerUrl(): string
    {
        return self::LIVE_SERVER_URL;
    }

    public function isValidConfiguration(): bool
    {
        return $this->clientId && $this->clientSecret && $this->webHookSecret;
    }
}
