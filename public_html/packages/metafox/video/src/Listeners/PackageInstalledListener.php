<?php

namespace MetaFox\Video\Listeners;

use Illuminate\Support\Arr;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Platform\PackageManager;
use MetaFox\Video\Models\VideoService;

class PackageInstalledListener
{
    /**
     * @param  string $package
     * @return void
     */
    public function handle(string $package): void
    {
        $this->seedEncoderServices($package);
        $this->removeOldMuxDrivers($package);
    }

    private function seedEncoderServices(string $package): void
    {
        $config = PackageManager::getConfig($package);

        $defaultProvider = null;
        if ($package === 'metafox/video') {
            $defaultProvider = Arr::get($config, 'default_provider');
        }

        $providers = $config['video_service_providers'] ?? [];

        if (!empty($providers)) {
            foreach ($providers as $driver => $provider) {
                $provider['is_active'] = 1;
                if ($defaultProvider == $driver) {
                    $provider['is_default'] = 1;
                }

                VideoService::query()->updateOrCreate(['driver' => $driver], $provider);
            }
        }
    }

    protected function removeOldMuxDrivers(string $package): void
    {
        if ($package !== 'metafox/video') {
            return;
        }
        resolve(DriverRepositoryInterface::class)
            ->getModel()
            ->newModelQuery()
            ->where('package_id', '=', $package)
            ->where('name', '=', 'video.mux')
            ->delete();
    }
}
