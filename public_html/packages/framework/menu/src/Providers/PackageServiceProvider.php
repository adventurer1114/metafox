<?php

namespace MetaFox\Menu\Providers;

use Illuminate\Support\ServiceProvider;
use MetaFox\Menu\Repositories\Eloquent\MenuItemRepository;
use MetaFox\Menu\Repositories\Eloquent\MenuRepository;
use MetaFox\Menu\Repositories\MenuItemRepositoryInterface;
use MetaFox\Menu\Repositories\MenuRepositoryInterface;

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
    public $bindings = [
        'menu'                             => MenuRepositoryInterface::class,
        MenuRepositoryInterface::class     => MenuRepository::class,
        MenuItemRepositoryInterface::class => MenuItemRepository::class,
    ];

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
//        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        // Boot facades.
    }
}
