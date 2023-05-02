<?php

namespace App\Console\Commands;

use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Foundation\Console\ConfigCacheCommand as Command;
use Illuminate\Support\Facades\DB;
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

            app('config')->set($values);

            $this->refreshApiKeys();
        } catch (\Exception $exception) {
            Log::channel('dev')->emergency($exception->getMessage());
        }

        return $app['config']->all();
    }

    public function refreshApiKeys()
    {
        $apiKey    = config('app.api_key');
        $apiSecret = config('app.api_secret');
        $secret    = DB::table('oauth_clients')->where('id', $apiKey)->value('secret');

        if ($secret === $apiSecret) {
            return;
        }

        DB::table('oauth_clients')->where('id', $apiKey)->update(['secret' => $apiSecret]);
    }
}
