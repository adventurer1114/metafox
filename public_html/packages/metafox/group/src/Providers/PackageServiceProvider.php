<?php

namespace MetaFox\Group\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Group\Contracts\MemberContract;
use MetaFox\Group\Contracts\RuleSupportContract;
use MetaFox\Group\Contracts\SupportContract;
use MetaFox\Group\Contracts\UserDataInterface;
use MetaFox\Group\Models\Block;
use MetaFox\Group\Models\Category;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Models\GroupChangePrivacy;
use MetaFox\Group\Models\GroupInviteCode;
use MetaFox\Group\Models\GroupText;
use MetaFox\Group\Models\Invite;
use MetaFox\Group\Models\Member;
use MetaFox\Group\Models\Question;
use MetaFox\Group\Models\Request;
use MetaFox\Group\Observers\BlockObserver;
use MetaFox\Group\Observers\CategoryObserver;
use MetaFox\Group\Observers\GroupInviteCodeObserver;
use MetaFox\Group\Observers\GroupObserver;
use MetaFox\Group\Observers\InviteObserver;
use MetaFox\Group\Observers\MemberObserver;
use MetaFox\Group\Observers\QuestionObserver;
use MetaFox\Group\Observers\RequestObserver;
use MetaFox\Group\Repositories\AnnouncementRepositoryInterface;
use MetaFox\Group\Repositories\BlockRepositoryInterface;
use MetaFox\Group\Repositories\CategoryRepositoryInterface;
use MetaFox\Group\Repositories\Eloquent\AnnouncementRepository;
use MetaFox\Group\Repositories\Eloquent\BlockRepository;
use MetaFox\Group\Repositories\Eloquent\CategoryRepository;
use MetaFox\Group\Repositories\Eloquent\ExampleRuleRepository;
use MetaFox\Group\Repositories\Eloquent\GroupChangePrivacyRepository;
use MetaFox\Group\Repositories\Eloquent\GroupInviteCodeRepository;
use MetaFox\Group\Repositories\Eloquent\GroupRepository;
use MetaFox\Group\Repositories\Eloquent\InviteRepository;
use MetaFox\Group\Repositories\Eloquent\MemberRepository;
use MetaFox\Group\Repositories\Eloquent\MuteRepository;
use MetaFox\Group\Repositories\Eloquent\QuestionRepository;
use MetaFox\Group\Repositories\Eloquent\RequestRepository;
use MetaFox\Group\Repositories\Eloquent\RuleRepository;
use MetaFox\Group\Repositories\ExampleRuleRepositoryInterface;
use MetaFox\Group\Repositories\GroupChangePrivacyRepositoryInterface;
use MetaFox\Group\Repositories\GroupInviteCodeRepositoryInterface;
use MetaFox\Group\Repositories\GroupRepositoryInterface;
use MetaFox\Group\Repositories\InviteRepositoryInterface;
use MetaFox\Group\Repositories\MemberRepositoryInterface;
use MetaFox\Group\Repositories\MuteRepositoryInterface;
use MetaFox\Group\Repositories\QuestionRepositoryInterface;
use MetaFox\Group\Repositories\RequestRepositoryInterface;
use MetaFox\Group\Repositories\RuleRepositoryInterface;
use MetaFox\Group\Support\MemberSupport;
use MetaFox\Group\Support\RuleSupport;
use MetaFox\Group\Support\Support;
use MetaFox\Group\Support\UserData;
use MetaFox\Platform\Support\EloquentModelObserver;

/**
 * Class GroupServiceProvider.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
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
            Group::ENTITY_TYPE              => Group::class,
            Member::ENTITY_TYPE             => Member::class,
            Invite::ENTITY_TYPE             => Invite::class,
            Request::ENTITY_TYPE            => Request::class,
            Block::ENTITY_TYPE              => Block::class,
            GroupChangePrivacy::ENTITY_TYPE => GroupChangePrivacy::class,
            GroupInviteCode::ENTITY_TYPE    => GroupInviteCode::class,
        ]);

        Group::observe([EloquentModelObserver::class, GroupObserver::class]);
        GroupText::observe([EloquentModelObserver::class]);
        Member::observe([EloquentModelObserver::class, MemberObserver::class]);
        Category::observe([CategoryObserver::class]);
        Question::observe([QuestionObserver::class]);
        Invite::observe([EloquentModelObserver::class, InviteObserver::class]);
        Block::observe([EloquentModelObserver::class, BlockObserver::class]);
        GroupChangePrivacy::observe([EloquentModelObserver::class]);
        GroupInviteCode::observe([EloquentModelObserver::class, GroupInviteCodeObserver::class]);
        Request::observe([EloquentModelObserver::class, RequestObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(GroupRepositoryInterface::class, GroupRepository::class);
        $this->app->bind(MemberRepositoryInterface::class, MemberRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(InviteRepositoryInterface::class, InviteRepository::class);
        $this->app->bind(RequestRepositoryInterface::class, RequestRepository::class);
        $this->app->bind(QuestionRepositoryInterface::class, QuestionRepository::class);
        $this->app->bind(RuleRepositoryInterface::class, RuleRepository::class);
        $this->app->bind(ExampleRuleRepositoryInterface::class, ExampleRuleRepository::class);
        $this->app->bind(BlockRepositoryInterface::class, BlockRepository::class);
        $this->app->bind(UserDataInterface::class, UserData::class);
        $this->app->bind(SupportContract::class, Support::class);
        $this->app->bind(RuleSupportContract::class, RuleSupport::class);
        $this->app->bind(GroupChangePrivacyRepositoryInterface::class, GroupChangePrivacyRepository::class);
        $this->app->bind(MemberContract::class, MemberSupport::class);
        $this->app->bind(GroupInviteCodeRepositoryInterface::class, GroupInviteCodeRepository::class);
        $this->app->bind(MuteRepositoryInterface::class, MuteRepository::class);
        $this->app->bind(AnnouncementRepositoryInterface::class, AnnouncementRepository::class);
    }
}
