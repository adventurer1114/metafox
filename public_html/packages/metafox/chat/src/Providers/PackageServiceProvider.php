<?php

namespace MetaFox\Chat\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Chat\Models\Message;
use MetaFox\Chat\Models\Room;
use MetaFox\Chat\Models\Subscription;
use MetaFox\Chat\Observers\RoomObserver;
use MetaFox\Chat\Repositories\Eloquent\MessageRepository;
use MetaFox\Chat\Repositories\Eloquent\RoomRepository;
use MetaFox\Chat\Repositories\Eloquent\SubscriptionRepository;
use MetaFox\Chat\Repositories\MessageRepositoryInterface;
use MetaFox\Chat\Repositories\RoomRepositoryInterface;
use MetaFox\Chat\Repositories\SubscriptionRepositoryInterface;
use MetaFox\Platform\Support\EloquentModelObserver;

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
            Room::ENTITY_TYPE         => Room::class,
            Message::ENTITY_TYPE      => Message::class,
            Subscription::ENTITY_TYPE => Subscription::class,
        ]);

        Room::observe([EloquentModelObserver::class, RoomObserver::class]);
        Subscription::observe([EloquentModelObserver::class]);
        Message::observe([EloquentModelObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
        $this->app->bind(RoomRepositoryInterface::class, RoomRepository::class);
        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
        // Boot facades.
    }
}
