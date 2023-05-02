<?php

namespace MetaFox\Authorization\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Authorization\Repositories\Contracts\PermissionRepositoryInterface;
use MetaFox\Authorization\Repositories\Contracts\RoleRepositoryInterface;
use MetaFox\Authorization\Repositories\DeviceAdminRepositoryInterface;
use MetaFox\Authorization\Repositories\DeviceRepositoryInterface;
use MetaFox\Authorization\Repositories\Eloquent\DeviceAdminRepository;
use MetaFox\Authorization\Repositories\Eloquent\DeviceRepository;
use MetaFox\Authorization\Repositories\Eloquent\PermissionRepository;
use MetaFox\Authorization\Repositories\Eloquent\PermissionSettingRepository;
use MetaFox\Authorization\Repositories\Eloquent\RoleRepository;
use MetaFox\Authorization\Repositories\PermissionSettingRepositoryInterface;

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
     * @var string[]
     */
    public array $bindings = [
        DeviceAdminRepositoryInterface::class => DeviceAdminRepository::class,
        DeviceRepositoryInterface::class      => DeviceRepository::class,
    ];

    /**
     * @var string[]
     */
    public array $singletons = [
        'perms' => PermissionSettingRepositoryInterface::class,
    ];

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
        Relation::morphMap([
            //            Blog::ENTITY_TYPE => Blog::class,
        ]);

//        Blog::observe([EloquentModelObserver::class, BlogObserver::class]);
//        BlogText::observe([EloquentModelObserver::class]);
//        Category::observe([CategoryObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(PermissionSettingRepositoryInterface::class, PermissionSettingRepository::class);
    }
}
