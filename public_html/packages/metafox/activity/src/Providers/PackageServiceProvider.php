<?php

namespace MetaFox\Activity\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Activity\Contracts\ActivityFeedContract;
use MetaFox\Activity\Contracts\ActivityHiddenManager as ActivityHiddenManagerContract;
use MetaFox\Activity\Contracts\ActivityPinManager as ActivityPinManagerContract;
use MetaFox\Activity\Contracts\ActivitySnoozeManager as ActivitySnoozeManagerContract;
use MetaFox\Activity\Contracts\TypeManager as TypeManagerContract;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Hidden;
use MetaFox\Activity\Models\Post;
use MetaFox\Activity\Models\Share;
use MetaFox\Activity\Models\Snooze;
use MetaFox\Activity\Observers\FeedObserver;
use MetaFox\Activity\Observers\PostObserver;
use MetaFox\Activity\Observers\ShareObserver;
use MetaFox\Activity\Observers\SnoozeObserver;
use MetaFox\Activity\Policies\FeedPolicy;
use MetaFox\Activity\Policies\SnoozePolicy;
use MetaFox\Activity\Repositories\ActivityHistoryRepositoryInterface;
use MetaFox\Activity\Repositories\Eloquent\ActivityHistoryRepository;
use MetaFox\Activity\Repositories\Eloquent\FeedRepository;
use MetaFox\Activity\Repositories\Eloquent\PinRepository;
use MetaFox\Activity\Repositories\Eloquent\ShareRepository;
use MetaFox\Activity\Repositories\Eloquent\SnoozeRepository;
use MetaFox\Activity\Repositories\Eloquent\TypeRepository;
use MetaFox\Activity\Repositories\FeedRepositoryInterface;
use MetaFox\Activity\Repositories\PinRepositoryInterface;
use MetaFox\Activity\Repositories\Eloquent\PostRepository;
use MetaFox\Activity\Repositories\PostRepositoryInterface;
use MetaFox\Activity\Repositories\ShareRepositoryInterface;
use MetaFox\Activity\Repositories\SnoozeRepositoryInterface;
use MetaFox\Activity\Repositories\TypeRepositoryInterface;
use MetaFox\Activity\Support\ActivityFeed;
use MetaFox\Activity\Support\ActivityHiddenManager;
use MetaFox\Activity\Support\ActivityPinManager;
use MetaFox\Activity\Support\ActivitySnoozeManager;
use MetaFox\Activity\Support\ActivitySubscription;
use MetaFox\Activity\Support\TypeManager;
use MetaFox\Platform\Support\EloquentModelObserver;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PackageServiceProvider extends ServiceProvider
{
    public array $bindings = [
        PinRepositoryInterface::class => PinRepository::class,
    ];

    public array $singletons = [
        'activity.pin' => PinRepositoryInterface::class,
    ];

    public function boot(): void
    {
        Feed::observe(FeedObserver::class);
        Post::observe([EloquentModelObserver::class, PostObserver::class]);
        Share::observe([ShareObserver::class, EloquentModelObserver::class]);
        Snooze::observe(SnoozeObserver::class);

        /*
         * Register entities
         */
        Relation::morphMap([
            Feed::ENTITY_TYPE          => Feed::class,
            Post::ENTITY_TYPE          => Post::class,
            Share::ENTITY_TYPE         => Share::class,
            Share::IMPORT_ENTITY_TYPE  => Share::class,
            Feed::IMPORT_ENTITY_TYPE   => Feed::class,
            Hidden::IMPORT_ENTITY_TYPE => Hidden::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FeedRepositoryInterface::class, FeedRepository::class);
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(SnoozeRepositoryInterface::class, SnoozeRepository::class);
        $this->app->bind(ShareRepositoryInterface::class, ShareRepository::class);
        $this->app->bind(TypeRepositoryInterface::class, TypeRepository::class);
        $this->app->bind(ActivityHistoryRepositoryInterface::class, ActivityHistoryRepository::class);
        // Boot facades.
        $this->app->bind(ActivityFeedContract::class, ActivityFeed::class);
        $this->app->bind('Activity.Subscription', ActivitySubscription::class);
        $this->app->singleton(TypeManagerContract::class, TypeManager::class);

        $this->app->singleton('FeedPolicySingleton', FeedPolicy::class);
        $this->app->singleton('SnoozePolicySingleton', SnoozePolicy::class);

        $this->app->singleton(ActivitySnoozeManagerContract::class, ActivitySnoozeManager::class);
        $this->app->singleton(ActivityHiddenManagerContract::class, ActivityHiddenManager::class);
        $this->app->singleton(ActivityPinManagerContract::class, ActivityPinManager::class);
    }
}
