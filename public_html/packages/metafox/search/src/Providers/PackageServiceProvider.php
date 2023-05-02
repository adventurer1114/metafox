<?php

namespace MetaFox\Search\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Search\Contracts\TypeManager as TypeManagerContract;
use MetaFox\Search\Models\Search;
use MetaFox\Search\Observers\SearchObserver;
use MetaFox\Search\Repositories\Eloquent\SearchRepository;
use MetaFox\Search\Repositories\Eloquent\TypeRepository;
use MetaFox\Search\Repositories\SearchRepositoryInterface;
use MetaFox\Search\Repositories\TypeRepositoryInterface;
use MetaFox\Search\Support\TypeManager;

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
            Search::ENTITY_TYPE => Search::class,
        ]);

        Search::observe([SearchObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SearchRepositoryInterface::class, SearchRepository::class);
        $this->app->bind(TypeRepositoryInterface::class, TypeRepository::class);
        // Boot facades.
        $this->app->bind(TypeManagerContract::class, TypeManager::class);
    }
}
