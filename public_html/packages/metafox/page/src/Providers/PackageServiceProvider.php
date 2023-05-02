<?php

namespace MetaFox\Page\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Page\Contracts\PageContract;
use MetaFox\Page\Contracts\PageMembershipInterface;
use MetaFox\Page\Models\Block;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Models\PageClaim;
use MetaFox\Page\Models\PageInvite;
use MetaFox\Page\Models\PageMember;
use MetaFox\Page\Models\PageText;
use MetaFox\Page\Observers\BlockObserver;
use MetaFox\Page\Observers\PageInviteObserver;
use MetaFox\Page\Observers\PageMemberObserver;
use MetaFox\Page\Observers\PageObserver;
use MetaFox\Page\Repositories\BlockRepositoryInterface;
use MetaFox\Page\Repositories\Eloquent\BlockRepository;
use MetaFox\Page\Repositories\Eloquent\PageCategoryRepository;
use MetaFox\Page\Repositories\Eloquent\PageClaimRepository;
use MetaFox\Page\Repositories\Eloquent\PageInviteRepository;
use MetaFox\Page\Repositories\Eloquent\PageMemberRepository;
use MetaFox\Page\Repositories\Eloquent\PageRepository;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;
use MetaFox\Page\Repositories\PageClaimRepositoryInterface;
use MetaFox\Page\Repositories\PageInviteRepositoryInterface;
use MetaFox\Page\Repositories\PageMemberRepositoryInterface;
use MetaFox\Page\Repositories\PageRepositoryInterface;
use MetaFox\Page\Support\PageMembership;
use MetaFox\Page\Support\PageSupport;
use MetaFox\Platform\Support\EloquentModelObserver;

/**
 * Class PackageServiceProvider.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
            Page::ENTITY_TYPE       => Page::class,
            PageMember::ENTITY_TYPE => PageMember::class,
            PageInvite::ENTITY_TYPE => PageInvite::class,
            Block::ENTITY_TYPE      => Block::class,
            PageClaim::ENTITY_TYPE  => PageClaim::class,
        ]);

        Page::observe([EloquentModelObserver::class, PageObserver::class]);
        PageText::observe([EloquentModelObserver::class]);
        PageMember::observe([PageMemberObserver::class, EloquentModelObserver::class]);
        PageClaim::observe([EloquentModelObserver::class]);
        PageInvite::observe([PageInviteObserver::class, EloquentModelObserver::class]);
        Block::observe([EloquentModelObserver::class, BlockObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PageInviteRepositoryInterface::class, PageInviteRepository::class);
        $this->app->bind(PageRepositoryInterface::class, PageRepository::class);
        $this->app->bind(PageCategoryRepositoryInterface::class, PageCategoryRepository::class);
        $this->app->bind(PageMemberRepositoryInterface::class, PageMemberRepository::class);
        $this->app->bind(PageMembershipInterface::class, PageMembership::class);
        $this->app->bind(PageContract::class, PageSupport::class);
        $this->app->bind(BlockRepositoryInterface::class, BlockRepository::class);
        $this->app->bind(PageClaimRepositoryInterface::class, PageClaimRepository::class);
    }
}
