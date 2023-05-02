<?php

namespace App\Console\Commands;

use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Foundation\Console\ConfigCacheCommand as Command;
use Illuminate\Support\Facades\Log;
use MetaFox\Platform\Contracts\SiteSettingRepositoryInterface;

class ConfigCacheCommand extends Command
{
    /**
     * Boot a fresh copy of the application configuration.
     *
     * @return array
     */
    protected function getFreshConfiguration()
    {
        $app = require $this->laravel->bootstrapPath() . '/app.php';

        $app->useStoragePath($this->laravel->storagePath());

        $app->make(ConsoleKernelContract::class)->bootstrap();

        // overlap setting configuration

        try {
            $values = resolve(SiteSettingRepositoryInterface::class)
                ->loadConfigValues();

            Log::channel('dev')->info(var_export($values, true));

            app('config')->set($values);
        } catch (\Exception $exception) {
            Log::channel('dev')->emergency($exception->getMessage());
        }

        return $app['config']->all();
    }
}
