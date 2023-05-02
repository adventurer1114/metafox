<?php

namespace MetaFox\Mux\Providers;

use MetaFox\Platform\Support\EloquentModelObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

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
//        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        // Boot facades.
    }
}
