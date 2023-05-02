<?php

namespace MetaFox\Event\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Event\Contracts\EventContract;
use MetaFox\Event\Contracts\EventInviteContract;
use MetaFox\Event\Contracts\EventMembershipContract;
use MetaFox\Event\Models\Event;
use MetaFox\Event\Models\EventText;
use MetaFox\Event\Models\HostInvite;
use MetaFox\Event\Models\Invite;
use MetaFox\Event\Models\Member;
use MetaFox\Event\Observers\EventObserver;
use MetaFox\Event\Observers\InviteObserver;
use MetaFox\Event\Observers\MemberObserver;
use MetaFox\Event\Repositories\CategoryRepositoryInterface;
use MetaFox\Event\Repositories\Eloquent\CategoryRepository;
use MetaFox\Event\Repositories\Eloquent\EventRepository;
use MetaFox\Event\Repositories\Eloquent\HostInviteRepository;
use MetaFox\Event\Repositories\Eloquent\InviteCodeRepository;
use MetaFox\Event\Repositories\Eloquent\InviteRepository;
use MetaFox\Event\Repositories\Eloquent\MemberRepository;
use MetaFox\Event\Repositories\EventRepositoryInterface;
use MetaFox\Event\Repositories\HostInviteRepositoryInterface;
use MetaFox\Event\Repositories\InviteCodeRepositoryInterface;
use MetaFox\Event\Repositories\InviteRepositoryInterface;
use MetaFox\Event\Repositories\MemberRepositoryInterface;
use MetaFox\Event\Support\Event as SupportEvent;
use MetaFox\Event\Support\EventInvite;
use MetaFox\Event\Support\EventMembership;
use MetaFox\Platform\Support\EloquentModelObserver;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'Event';

    /**
     * @var string
     */
    protected $moduleNameLower = 'event';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            Event::ENTITY_TYPE      => Event::class,
            Invite::ENTITY_TYPE     => Invite::class,
            HostInvite::ENTITY_TYPE => HostInvite::class,
            Member::ENTITY_TYPE     => Member::class,
        ]);

        Event::observe([EloquentModelObserver::class, EventObserver::class]);
        EventText::observe([EloquentModelObserver::class]);
        Member::observe([EloquentModelObserver::class, MemberObserver::class]);
        Invite::observe([EloquentModelObserver::class, InviteObserver::class]);
        HostInvite::observe([EloquentModelObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(InviteRepositoryInterface::class, InviteRepository::class);
        $this->app->bind(HostInviteRepositoryInterface::class, HostInviteRepository::class);
        $this->app->bind(MemberRepositoryInterface::class, MemberRepository::class);
        $this->app->bind(InviteCodeRepositoryInterface::class, InviteCodeRepository::class);

        $this->app->singleton(EventContract::class, SupportEvent::class);
        $this->app->singleton(EventMembershipContract::class, EventMembership::class);
        $this->app->singleton(EventInviteContract::class, EventInvite::class);
    }
}
