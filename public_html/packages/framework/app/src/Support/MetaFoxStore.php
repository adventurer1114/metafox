<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\App\Support;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use MetaFox\App\Repositories\PackageRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFox;
use MetaFox\Platform\MetaFoxConstant;

/**
 * Class MetaFoxStore.
 */
class MetaFoxStore
{
    private const TIMEOUT = 30;

    private ?string $licenseId;

    private ?string $licenseKey;

    private string $baseUrl = 'https://api.phpfox.com';

    private array $apiStoreHeaders = [
        'X-Product'     => 'metafox',
        'X-Namespace'   => 'phpfox',
        'X-API-Version' => '1.1',
    ];

    private array $apiClientAreaHeaders = [
        'X-Product'     => 'metafox',
        'X-Namespace'   => 'expert',
        'X-API-Version' => '1.1',
    ];

    private PackageRepositoryInterface $packageRepository;

    public function __construct()
    {
        $this->licenseId         = Settings::get('core.license.id');
        $this->licenseKey        = Settings::get('core.license.key');
        $this->packageRepository = resolve('core.packages');
    }

    /**
     * browse.
     *
     * @param  array $params
     * @return array
     */
    public function browse(array $params = []): array
    {
        $requestUrl = $this->baseUrl
            . '/products/browse?'
            . Arr::query(array_merge([
                'version=' . MetaFoxConstant::VERSION,
            ], $params));

        $response = Http::asJson()
            ->timeout(self::TIMEOUT)
            ->withHeaders($this->apiStoreHeaders)
            ->withBasicAuth($this->licenseId, $this->licenseKey)
            ->get($requestUrl)
            ->json();

        if (empty($response)) {
            return [];
        }

        $data = $response['data'] ?? [];

        foreach ($data as $index => $item) {
            $data[$index]['module_name']   = 'app';
            $data[$index]['resource_name'] = 'app_store_product';
        }

        return $data;
    }

    public function purchased(): array
    {
        $response = Http::asJson()
            ->timeout(self::TIMEOUT)
            ->withHeaders($this->apiStoreHeaders)
            ->withBasicAuth($this->licenseId, $this->licenseKey)
            ->get($this->baseUrl . '/purchased')
            ->json();

        $data = $response['data'] ?? [];

        foreach ($data as $index => $item) {
            $data[$index]['module_name']   = 'app';
            $data[$index]['resource_name'] = 'app_store_product';

            $this->verifyProductStatus($item);
        }

        return $data;
    }

    private function verifyProductStatus(?array $item): void
    {
        if (!$item) {
            return;
        }

        $package        = $this->packageRepository->findByName($item['identity']);
        $expirationDate = Arr::get($item, 'expired_at');

        if ($package) {
            $package->latest_version = $item['version'];
            $package->expired_at     = !empty($expirationDate) ? $expirationDate : null;
            $package->saveQuietly();
        }
    }

    public function show($id)
    {
        $endpoint = $this->baseUrl . '/product/' . $id;
        $params   = ['version' => MetaFox::getVersion()];
        $request  = Http::asJson()
            ->timeout(self::TIMEOUT)
            ->withHeaders($this->apiStoreHeaders)
            ->withBasicAuth($this->licenseId, $this->licenseKey)
            ->get($endpoint, $params);

        Log::channel('installation')
            ->debug('Show detail ' . $endpoint, $params);

        $json = $request->json();

        Log::channel('dev')->debug('product detail ', $json);

        $data                  = $json['data'] ?? [];
        $data['module_name']   = 'app';
        $data['resource_name'] = 'app_store_product';
        $data['purchase_url']  = $this->purchaseUrl($id);

        $package = $this->packageRepository->findByName($data['identity'] ?? '');

        if ($package) {
            $data['bundle_status']   = $package->bundle_status;
            $data['current_version'] = $package->version;
        } else {
            $data['bundle_status'] = 'unknown';
        }

        return $data;
    }

    public function purchaseUrl($id): ?string
    {
        $returnUrl = config('app.url') . '/admincp/app/store/product/' . $id;

        $request = Http::asJson()
            ->timeout(self::TIMEOUT)
            ->withHeaders($this->apiStoreHeaders)
            ->withBasicAuth($this->licenseId, $this->licenseKey)
            ->get($this->baseUrl . '/purchase/' . $id, ['return_url' => $returnUrl]);

        $json = $request->json();

        return Arr::get($json, 'data.purchase_url');
    }

    public function publishToStore(string $package, string $version, string $name, string $file, string $channel): void
    {
        $token = config('app.mfox_store_api_token');

        if (!$token) {
            Log::channel('dev')->debug('missing environment variable "MFOX_STORE_API_TOKEN"');

            return;
        }

        $serviceUrl = $this->baseUrl . '/product/version/add';

        $fileSize = number_format(filesize($file) / 1024 / 1024, 2);

        Log::channel('dev')->debug(sprintf(
            'Upload "%s", filename: %s, filesize: %s Mb',
            $serviceUrl,
            $name,
            $fileSize
        ));

        $request = Http::asMultipart()
            ->withToken($token)
            ->withHeaders($this->apiClientAreaHeaders)
            ->attach('version_package', file_get_contents($file), $name)
            ->post($serviceUrl, [
                'version_type'    => 'backend',
                'version'         => $version,
                'description'     => sprintf('Updated %s', $version),
                'release_channel' => $channel,
                'id'              => $package,
            ]);

        $json = $request->json();

        if (!is_array($json)) {
            Log::channel('dev')->debug($request->body());
        } elseif (Arr::get($json, 'status') == 'failed') {
            Log::channel('dev')->debug(json_encode($json, JSON_PRETTY_PRINT));
        } else {
            Log::channel('dev')->debug(json_encode($json, JSON_PRETTY_PRINT));
        }
    }

    public function downloadProduct(string $name, ?string $version, string $channel): string
    {
        Log::channel(sprintf('Downloading %s:%s', $name, $version));

        $post = [
            'version'         => MetaFoxConstant::VERSION,
            'app_version'     => $version,
            'version_type'    => 'backend',
            'release_channel' => $channel,
            'id'              => $name,
        ];

        if ($version) {
            $post['app_version'] = $version;
        }

        $request = Http::asJson()
            ->timeout(self::TIMEOUT)
            ->withBasicAuth($this->licenseId, $this->licenseKey)
            ->withHeaders($this->apiStoreHeaders)
            ->post($this->baseUrl . '/install', $post);

        $json = $request->json();
        $json = $json['data'] ?? $json;

        Log::channel('dev')->debug(sprintf('Download product "%s"', $name), $json);

        $downloadUrl = $json['download'] ?? null;

        if (!$downloadUrl) {
            throw new InvalidArgumentException('Could not download the product.' . PHP_EOL . $request->body());
        }

        $filename = tempnam(sys_get_temp_dir(), 'metafox-product-' . $json['id']) . '.zip';
        $stream   = fopen($filename, 'w');

        if (!$stream) {
            throw new \InvalidArgumentException(sprintf('Failed opening stream "%s" to write.', $filename));
        }

        $content = file_get_contents($downloadUrl);

        if (!$content) {
            throw new \InvalidArgumentException(sprintf('Failed opening download "%s".', $downloadUrl));
        }

        if (!fwrite($stream, $content)) {
            throw new \InvalidArgumentException(sprintf('Failed writing to "%s".', $filename));
        }

        fclose($stream);

        return $filename;
    }

    public function downloadFramework(string $channel, string $filename)
    {
        $temporary = $filename . '.temp';

        // migration.
        register_shutdown_function(function () use ($filename, $temporary) {
            if (file_exists($temporary)) {
                @copy($temporary, $filename);
                @unlink($temporary);
            }
            Log::channel('dev')->debug('downloadFramework');
        });

        $json = Http::asJson()
            ->timeout(self::TIMEOUT)
            ->withBasicAuth($this->licenseId, $this->licenseKey)
            ->withHeaders($this->apiStoreHeaders)
            ->post($this->baseUrl . '/phpfox-download')
            ->json();

        $downloadUrl = $json['download'] ?? null;

        if (!$downloadUrl) {
            throw new \RuntimeException('Missing [download] url.');
        }

        $source = fopen($downloadUrl, 'r');
        $dest   = fopen($temporary, 'w');
        stream_copy_to_stream($source, $dest);
    }

    public function verifyExpiredProducts(): void
    {
        $response = Http::asJson()
            ->timeout(self::TIMEOUT)
            ->withBasicAuth($this->licenseId, $this->licenseKey)
            ->withHeaders($this->apiStoreHeaders)
            ->get($this->baseUrl . '/purchased')
            ->json();

        $data = $response['data'];
        foreach ($data as $item) {
            try {
                $this->verifyProduct($item);
            } catch (\Exception) {
                // don't break error
            }
        }
    }

    public function verifyLicense(string $licenseId, string $licenseKey, string $installPath = '/'): bool
    {
        $response = Http::asJson()
            ->timeout(self::TIMEOUT)
            ->withBasicAuth($licenseId, $licenseKey)
            ->withHeaders($this->apiStoreHeaders)
            ->post($this->baseUrl . '/verify', [
                'url'               => config('app.url'),
                'installation_path' => $installPath,
            ]);

        $json = $response->json();

        if (Arr::get($json, 'valid')) {
            return true;
        }

        throw new \InvalidArgumentException($response->body());
    }

    private function verifyProduct(?array $item): void
    {
        if (empty($item)) {
            return;
        }

        Log::channel('dev')->debug('verifyProduct', $item);

        $identity       = $item['identity'];
        $package        = $this->packageRepository->findByName($identity);
        $expirationDate = Arr::get($item, 'expired_at');

        if (!$package) {
            return;
        }

        $package->is_purchased   = 1;
        $package->latest_version = $item['version'];
        $package->expired_at     = !empty($expirationDate) ? $expirationDate : null;
        $package->saveQuietly();
    }

    public function getInstalledListByType(string $type)
    {
        return array_filter(array_map(function ($data) use ($type) {
            if ($data['type'] === $type && !$data['core']) {
                return $data['name'];
            }

            return null;
        }, config('metafox.packages', [])), function ($data) {
            return (bool) $data;
        });
    }

    public function verifyLatestVersions()
    {
        $apps      = $this->getInstalledListByType('app');
        $themes    = $this->getInstalledListByType('theme');
        $languages = $this->getInstalledListByType('language');

        $response = Http::asJson()
            ->timeout(self::TIMEOUT)
            ->withBasicAuth($this->licenseId, $this->licenseKey)
            ->withHeaders($this->apiStoreHeaders)
            ->post($this->baseUrl . '/products', [
                'products' => [
                    'apps'      => [...$apps, 'metafox/framework'],
                    'themes'    => $themes,
                    'languages' => $languages,
                ],
                'version' => MetaFox::getVersion(),
            ]);

        $json = $response->json();

        if (!$json) {
            return;
        }

        Log::channel('dev')->debug($response->body());

        $this->updateLatestVersion(Arr::get($json, 'products.apps'));
    }

    private function updateLatestVersion($apps)
    {
        if (empty($apps)) {
            return;
        }

        foreach ($apps as $name => $app) {
            $package = $this->packageRepository->findByName($name);
            if (
                $package
                && isset($app['version'])
                && $package->latest_version != $app['version']
            ) {
                $package->latest_version = $app['version'];
                $package->saveQuietly();
            }
        }
    }

    public function verifyMetaFoxInfo()
    {
        try {
            // if installation for testing without info.
            if (!$this->licenseId || !$this->licenseKey || !$this->baseUrl) {
                return;
            }

            $response = Http::asJson()
                ->timeout(self::TIMEOUT)
                ->withBasicAuth($this->licenseId, $this->licenseKey)
                ->withHeaders($this->apiStoreHeaders)
                ->post($this->baseUrl . '/info');

            $json = $response->json();

            if (!$json) {
                return;
            }

            $expired = Arr::get($json, 'renewal_expired_date');

            if ($expired) {
                $expired = Carbon::createFromTimestamp($expired);
            } else {
                $expired = null;
            }

            Settings::save([
                'core.platform.expired_at'     => $expired,
                'core.platform.latest_version' => Arr::get($json, 'eligible_phpfox_version'),
            ]);

            Artisan::call('cache:reset');
        } catch (\Exception $exception) {
            Log::channel('dev')->debug($exception->getMessage());
        }
    }
}
