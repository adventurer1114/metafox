<?php

namespace MetaFox\Mfa\Providers;

use Illuminate\Support\ServiceProvider;
use MetaFox\Mfa\Contracts\Mfa as ContractsMfa;
use MetaFox\Mfa\Contracts\ServiceManagerInterface;
use MetaFox\Mfa\Repositories\Eloquent\ServiceRepository;
use MetaFox\Mfa\Repositories\Eloquent\UserAuthTokenRepository;
use MetaFox\Mfa\Repositories\Eloquent\UserServiceRepository;
use MetaFox\Mfa\Repositories\ServiceRepositoryInterface;
use MetaFox\Mfa\Repositories\UserAuthTokenRepositoryInterface;
use MetaFox\Mfa\Repositories\UserServiceRepositoryInterface;
use MetaFox\Mfa\Support\Facades\Mfa;
use MetaFox\Mfa\Support\ServiceManager;

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
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(UserServiceRepositoryInterface::class, UserServiceRepository::class);
        $this->app->bind(UserAuthTokenRepositoryInterface::class, UserAuthTokenRepository::class);

        $this->app->bind(ServiceManagerInterface::class, ServiceManager::class);

        $this->app->singleton(ContractsMfa::class, Mfa::class);
    }
}
