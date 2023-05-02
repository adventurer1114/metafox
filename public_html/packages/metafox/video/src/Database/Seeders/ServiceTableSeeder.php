<?php

namespace MetaFox\Video\Database\Seeders;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use MetaFox\Platform\PackageManager;
use MetaFox\Video\Models\VideoService;

/**
 * Class ServiceTableSeeder.
 */
class ServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function run()
    {
        if (VideoService::query()->exists()) {
            return;
        }

        $config = PackageManager::getConfig('metafox/video');

        $defaultProvider = config('app.mfox_default_video_service');

        $providers = $config['providers'] ?? [];

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
}
