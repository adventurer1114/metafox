<?php

namespace MetaFox\Mobile\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Mobile\Repositories\AdMobConfigAdminRepositoryInterface;
use MetaFox\Mobile\Repositories\AdMobPageAdminRepositoryInterface;
use MetaFox\Mobile\Repositories\Eloquent\AdMobConfigAdminRepository;
use MetaFox\Mobile\Repositories\Eloquent\AdMobPageAdminRepository;

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
        /*
         * Register relation
         */
        Relation::morphMap([]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Boot Repositories
        $this->app->bind(AdMobConfigAdminRepositoryInterface::class, AdMobConfigAdminRepository::class);
        $this->app->bind(AdMobPageAdminRepositoryInterface::class, AdMobPageAdminRepository::class);

        // Boot facades.
    }
}
