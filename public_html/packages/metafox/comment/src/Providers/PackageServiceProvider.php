<?php

namespace MetaFox\Comment\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Comment\Models\Comment;
use MetaFox\Comment\Models\CommentAttachment;
use MetaFox\Comment\Observers\CommentObserver;
use MetaFox\Comment\Repositories\CommentHistoryRepositoryInterface;
use MetaFox\Comment\Repositories\CommentRepositoryInterface;
use MetaFox\Comment\Repositories\Eloquent\CommentHistoryRepository;
use MetaFox\Comment\Repositories\Eloquent\CommentRepository;
use MetaFox\Platform\Support\EloquentModelObserver;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
        $this->app->bind(CommentHistoryRepositoryInterface::class, CommentHistoryRepository::class);
    }

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            Comment::ENTITY_TYPE => Comment::class,
        ]);

        Comment::observe([
            CommentObserver::class,
            EloquentModelObserver::class,
        ]);
        CommentAttachment::observe([EloquentModelObserver::class]);
    }
}
