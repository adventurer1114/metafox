<?php

namespace MetaFox\Sms\Providers;

use Illuminate\Support\ServiceProvider;
use MetaFox\Sms\Contracts\ManagerInterface;
use MetaFox\Sms\Support\SmsManager;

/**
 * --------------------------------------------------------------------------
 * Code Generator
 * --------------------------------------------------------------------------
 * stub: src/Providers/PackageServiceProvider.stub.
 */

/**
 * Class PackageServiceProvider.
 *
 * @ignore
 * @codeCoverageIgnore
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ManagerInterface::class, SmsManager::class);
    }
}
