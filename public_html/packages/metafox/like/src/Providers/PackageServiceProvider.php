<?php

namespace MetaFox\Like\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Like\Models\Like;
use MetaFox\Like\Observers\LikeObserver;
use MetaFox\Like\Repositories\Eloquent\LikeRepository;
use MetaFox\Like\Repositories\Eloquent\ReactionRepository;
use MetaFox\Like\Repositories\LikeRepositoryInterface;
use MetaFox\Like\Repositories\ReactionRepositoryInterface;
use MetaFox\Platform\Support\EloquentModelObserver;

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
        Relation::morphMap([
            Like::ENTITY_TYPE => Like::class,
        ]);

        Like::observe([
            LikeObserver::class,
            EloquentModelObserver::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(LikeRepositoryInterface::class, LikeRepository::class);
        $this->app->bind(ReactionRepositoryInterface::class, ReactionRepository::class);
    }
}
