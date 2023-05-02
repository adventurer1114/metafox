<?php

namespace MetaFox\BackgroundStatus\Providers;

use Illuminate\Support\ServiceProvider;
use MetaFox\BackgroundStatus\Models\BgsBackground;
use MetaFox\BackgroundStatus\Models\BgsCollection;
use MetaFox\BackgroundStatus\Observers\BgsBackgroundObserver;
use MetaFox\BackgroundStatus\Observers\BgsCollectionObserver;
use MetaFox\BackgroundStatus\Repositories\BgsBackgroundRepositoryInterface;
use MetaFox\BackgroundStatus\Repositories\BgsCollectionRepositoryInterface;
use MetaFox\BackgroundStatus\Repositories\Eloquent\BgsBackgroundRepository;
use MetaFox\BackgroundStatus\Repositories\Eloquent\BgsCollectionRepository;

/**
 * Class PackageServiceProvider.
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
        BgsCollection::observe([BgsCollectionObserver::class]);
        BgsBackground::observe([BgsBackgroundObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(BgsCollectionRepositoryInterface::class, BgsCollectionRepository::class);
        $this->app->bind(BgsBackgroundRepositoryInterface::class, BgsBackgroundRepository::class);
    }
}
