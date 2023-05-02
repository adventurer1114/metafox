<?php

namespace MetaFox\Notification\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\ChannelManager as IlluminateChannelManager;
use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;
use Illuminate\Notifications\Channels\MailChannel as IlluminateMailChannel;
use Illuminate\Support\ServiceProvider;
use MetaFox\Notification\Channels\DatabaseChannel;
use MetaFox\Notification\Channels\MailChannel;
use MetaFox\Notification\Contracts\TypeManager as TypeManagerContract;
use MetaFox\Notification\Models\Notification;
use MetaFox\Notification\Repositories\Contracts\WebpushSubscriptionRepositoryInterface;
use MetaFox\Notification\Repositories\Eloquent\NotificationChannelRepository;
use MetaFox\Notification\Repositories\Eloquent\NotificationModuleRepository;
use MetaFox\Notification\Repositories\Eloquent\NotificationRepository;
use MetaFox\Notification\Repositories\Eloquent\TypeChannelRepository;
use MetaFox\Notification\Repositories\Eloquent\TypeRepository;
use MetaFox\Notification\Repositories\NotificationChannelRepositoryInterface;
use MetaFox\Notification\Repositories\NotificationManager;
use MetaFox\Notification\Repositories\NotificationModuleRepositoryInterface;
use MetaFox\Notification\Repositories\NotificationRepositoryInterface;
use MetaFox\Notification\Repositories\TypeChannelRepositoryInterface;
use MetaFox\Notification\Repositories\TypeRepositoryInterface;
use MetaFox\Notification\Repositories\WebpushSubscriptionRepository;
use MetaFox\Notification\Support\ChannelManager;
use MetaFox\Notification\Support\TypeManager;
use MetaFox\Platform\Contracts\NotificationManagerInterface;

/**
 * Class PackageServiceProvider.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'Notification';

    /**
     * @var string
     */
    protected $moduleNameLower = 'notification';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            Notification::ENTITY_TYPE => Notification::class,
        ]);

        $this->app->instance(IlluminateDatabaseChannel::class, resolve(DatabaseChannel::class));
        $this->app->instance(IlluminateMailChannel::class, resolve(MailChannel::class));
        $this->app->instance(IlluminateChannelManager::class, resolve(ChannelManager::class));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(NotificationManagerInterface::class, NotificationManager::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(WebpushSubscriptionRepositoryInterface::class, WebpushSubscriptionRepository::class);
        $this->app->bind(TypeRepositoryInterface::class, TypeRepository::class);
        $this->app->bind(NotificationChannelRepositoryInterface::class, NotificationChannelRepository::class);
        $this->app->singleton(TypeManagerContract::class, TypeManager::class);
        $this->app->singleton('Notify', NotificationManager::class);
        $this->app->bind(NotificationModuleRepositoryInterface::class, NotificationModuleRepository::class);
        $this->app->bind(TypeChannelRepositoryInterface::class, TypeChannelRepository::class);
    }
}
