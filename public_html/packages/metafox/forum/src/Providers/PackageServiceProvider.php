<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Forum\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Forum\Contracts\ForumPostSupportContract;
use MetaFox\Forum\Contracts\ForumSupportContract;
use MetaFox\Forum\Contracts\ForumThreadSupportContract;
use MetaFox\Forum\Models\ForumPost;
use MetaFox\Forum\Models\ForumPostQuote;
use MetaFox\Forum\Models\ForumPostText;
use MetaFox\Forum\Models\ForumThread;
use MetaFox\Forum\Models\ForumThreadSubscribe;
use MetaFox\Forum\Models\ForumThreadText;
use MetaFox\Forum\Models\Moderator;
use MetaFox\Forum\Models\ModeratorAccess;
use MetaFox\Forum\Observers\ForumPostObserver;
use MetaFox\Forum\Observers\ForumThreadObserver;
use MetaFox\Forum\Repositories\Eloquent\ForumPostRepository;
use MetaFox\Forum\Repositories\Eloquent\ForumRepository;
use MetaFox\Forum\Repositories\Eloquent\ForumThreadLastReadRepository;
use MetaFox\Forum\Repositories\Eloquent\ForumThreadRepository;
use MetaFox\Forum\Repositories\Eloquent\ForumThreadSubscribeRepository;
use MetaFox\Forum\Repositories\ForumPostRepositoryInterface;
use MetaFox\Forum\Repositories\ForumRepositoryInterface;
use MetaFox\Forum\Repositories\ForumThreadLastReadRepositoryInterface;
use MetaFox\Forum\Repositories\ForumThreadRepositoryInterface;
use MetaFox\Forum\Repositories\ForumThreadSubscribeRepositoryInterface;
use MetaFox\Forum\Support\ForumPostSupport;
use MetaFox\Forum\Support\ForumSupport;
use MetaFox\Forum\Support\ForumThreadSupport;
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
        $this->app->bind(ForumRepositoryInterface::class, ForumRepository::class);
        $this->app->bind(ForumThreadRepositoryInterface::class, ForumThreadRepository::class);
        $this->app->bind(ForumPostRepositoryInterface::class, ForumPostRepository::class);
        $this->app->bind(ForumThreadSubscribeRepositoryInterface::class, ForumThreadSubscribeRepository::class);
        $this->app->bind(ForumPostSupportContract::class, ForumPostSupport::class);
        $this->app->bind(ForumThreadLastReadRepositoryInterface::class, ForumThreadLastReadRepository::class);
        $this->app->bind(ForumSupportContract::class, ForumSupport::class);
        $this->app->bind(ForumThreadSupportContract::class, ForumThreadSupport::class);
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            ForumThread::ENTITY_TYPE     => ForumThread::class,
            ForumPost::ENTITY_TYPE       => ForumPost::class,
            Moderator::ENTITY_TYPE       => Moderator::class,
            ModeratorAccess::ENTITY_TYPE => ModeratorAccess::class,
            'forum_post_quote'           => ForumPostQuote::class,
            'forum_thread_subscribe'     => ForumThreadSubscribe::class,
        ]);

        ForumPostText::observe([EloquentModelObserver::class]);
        ForumThreadText::observe([EloquentModelObserver::class]);
        ForumThread::observe([EloquentModelObserver::class, ForumThreadObserver::class]);
        ForumPost::observe([EloquentModelObserver::class, ForumPostObserver::class]);
    }
}
