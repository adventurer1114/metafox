<?php

namespace MetaFox\Video\Support\Providers;

use FFMpeg\Coordinate\FrameRate;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg as Driver;
use FFMpeg\Format\FormatInterface;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MetaFox\Core\Support\FileSystem\Image\Plugins\ResizeImage;
use MetaFox\Core\Support\FileSystem\UploadFile;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Storage\Models\StorageFile;
use MetaFox\Video\Contracts\VideoServiceInterface;
use MetaFox\Video\Models\Video as Model;

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class FFMPEG implements VideoServiceInterface
{
    /**
     * The FFMPEG driver.
     */
    private Driver $driver;

    public const PROVIDER_TYPE = 'ffmpeg';

    public function getProviderType(): string
    {
        return self::PROVIDER_TYPE;
    }

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $config = [
            'ffmpeg.binaries'  => Settings::get('video.ffmpeg.binaries'),
            'ffprobe.binaries' => Settings::get('video.ffprobe.binaries'),
            'ffmpeg.threads'   => Settings::get('video.ffmpeg.threads'),
            'timeout'          => Settings::get('video.ffmpeg.timeout'),
        ];

        if (
            empty($config['ffmpeg.binaries'])
            || empty($config['ffprobe.binaries'])
        ) {
            abort(500, 'Missing FFMPEG Configuration! Please recheck settings!');
        }

        $this->driver = Driver::create($config, Log::channel('video'));
    }

    /**
     * @inheritDoc
     */
    public function processVideo(StorageFile $file): array
    {
        // Reading video file
        $localFile = app('storage')->getAs($file->entityId());

        $outputPath = sprintf('%s_%s', tempnam(sys_get_temp_dir(), 'metafox'), $file->original_name);
        $video      = $this->driver->open($localFile);

        // Encode video
        $video->filters()
            ->resample(44100)                 // -ar 44100
            ->framerate(new FrameRate(25), 0) // -r 25
            ->synchronize();

        // Extracting a picture from the video
        $image     = null;
        $imagePath = tempnam(sys_get_temp_dir(), 'metafox') . '_thumbnail.jpg';
        $duration  = $video->getFFProbe()->format($localFile)->get('duration');
        $dimension = $video->getFFProbe()->streams($localFile)->videos()->first()->getDimensions();
        $width     = $dimension->getWidth();
        $height    = $dimension->getHeight();
        if ((int) $duration > 0) {
            $frame = $video->frame(TimeCode::fromSeconds($duration / 2));
            $frame->save($imagePath);

            $image = upload()
                ->setStorage('photo')
                ->setPath('video')
                ->setThumbSizes(['500'])
                ->setItemType('photo')
                ->setUser($file->user)
                ->storeFile(UploadFile::pathToUploadedFile($imagePath));
        }
        $video->save($this->buildFormat(), $outputPath);

        $videoFile = upload()
            ->setStorage('video')
            ->setPath('video')
            ->setItemType('video')
            ->setUser($file->user)
            ->storeFile(UploadFile::pathToUploadedFile($outputPath));

        $this->cleanUpTemp([$outputPath, $imagePath]);

        return [
            'image_file_id' => $image instanceof StorageFile ? $image->entityId() : null,
            'video_file_id' => $videoFile instanceof StorageFile ? $videoFile->entityId() : null,
            'duration'      => $duration,
            'resolution_x'  => (string) $width,
            'resolution_y'  => (string) $height,
            'in_process'    => Model::VIDEO_READY,
        ];
    }

    /**
     * @param  Request $request
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleWebhook(Request $request): bool
    {
        return true; // No webhook needed
    }

    protected function buildFormat(): FormatInterface
    {
        $format = new X264();

        return $format
            ->setBFramesSupport(false)
            ->setAudioKiloBitrate(64);
    }

    private function cleanUpTemp(array $files)
    {
        foreach ($files as $file) {
            if (file_exists($file)) { // ensure file exists or add @unlink file.
                @unlink($file);
            }
        }
    }

    public function executeApi(string $apiName, string $method = 'GET', bool $returnTransfer = false, string $postFields = ''): mixed
    {
        return null;
    }

    public function getLiveServerUrl(): string
    {
        return '';
    }

    public function getThumbnailPlayback(): string
    {
        return '';
    }

    public function getVideoPlayback(): string
    {
        return '';
    }

    public function isValidConfiguration(): bool
    {
        return Settings::get('video.ffmpeg.binaries') && Settings::get('video.ffprobe.binaries');
    }
}
