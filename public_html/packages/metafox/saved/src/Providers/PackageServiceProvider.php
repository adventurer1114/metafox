<?php

namespace MetaFox\Saved\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Saved\Contracts\Support\SavedTypeContract;
use MetaFox\Saved\Models\Saved;
use MetaFox\Saved\Models\SavedList;
use MetaFox\Saved\Observers\SavedListObserver;
use MetaFox\Saved\Observers\SavedObserver;
use MetaFox\Saved\Repositories\Eloquent\SavedAggRepository;
use MetaFox\Saved\Repositories\Eloquent\SavedListDataRepository;
use MetaFox\Saved\Repositories\Eloquent\SavedListItemViewRepository;
use MetaFox\Saved\Repositories\Eloquent\SavedListRepository;
use MetaFox\Saved\Repositories\Eloquent\SavedRepository;
use MetaFox\Saved\Repositories\Eloquent\SavedSearchRepository;
use MetaFox\Saved\Repositories\SavedAggRepositoryInterface;
use MetaFox\Saved\Repositories\SavedListDataRepositoryInterface;
use MetaFox\Saved\Repositories\SavedListItemViewRepositoryInterface;
use MetaFox\Saved\Repositories\SavedListRepositoryInterface;
use MetaFox\Saved\Repositories\SavedRepositoryInterface;
use MetaFox\Saved\Repositories\SavedSearchRepositoryInterface;
use MetaFox\Saved\Support\SavedType;

/**
 * Class PackageServiceProvider.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
        Relation::morphMap([
            Saved::ENTITY_TYPE     => Saved::class,
            SavedList::ENTITY_TYPE => SavedList::class,
        ]);

        Saved::observe([SavedObserver::class]);
        SavedList::observe([SavedListObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SavedRepositoryInterface::class, SavedRepository::class);
        $this->app->bind(SavedListRepositoryInterface::class, SavedListRepository::class);
        $this->app->bind(SavedAggRepositoryInterface::class, SavedAggRepository::class);
        $this->app->bind(SavedSearchRepositoryInterface::class, SavedSearchRepository::class);
        $this->app->bind(SavedListDataRepositoryInterface::class, SavedListDataRepository::class);
        $this->app->bind(SavedListItemViewRepositoryInterface::class, SavedListItemViewRepository::class);

        // Support classes binding
        $this->app->bind(SavedTypeContract::class, SavedType::class);
    }
}
