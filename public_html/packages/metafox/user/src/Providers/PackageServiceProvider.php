<?php

namespace MetaFox\User\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Authorization\Models\Role;
use MetaFox\Authorization\Observers\UserRoleObserver;
use MetaFox\Platform\Support\EloquentModelObserver;
use MetaFox\User\Contracts\PermissionRegistrar;
use MetaFox\User\Contracts\UserAuth as ContractsUserAuth;
use MetaFox\User\Contracts\UserBlockedSupportContract;
use MetaFox\User\Contracts\UserContract;
use MetaFox\User\Models\User;
use MetaFox\User\Models\UserBan;
use MetaFox\User\Models\UserBlocked;
use MetaFox\User\Models\UserPrivacy as UserPrivacyModel;
use MetaFox\User\Models\UserProfile;
use MetaFox\User\Models\UserShortcut;
use MetaFox\User\Observers\UserBanObserver;
use MetaFox\User\Observers\UserEntityObserver;
use MetaFox\User\Observers\UserObserver;
use MetaFox\User\Observers\UserProfileObserver;
use MetaFox\User\Repositories\AdminLoggedRepositoryInterface;
use MetaFox\User\Repositories\CancelFeedbackAdminRepositoryInterface;
use MetaFox\User\Repositories\CancelReasonRepositoryInterface;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Repositories\Eloquent\AdminLoggedRepository;
use MetaFox\User\Repositories\Eloquent\CancelFeedbackAdminRepository;
use MetaFox\User\Repositories\Eloquent\CancelReasonRepository;
use MetaFox\User\Repositories\Eloquent\MultiFactorTokenRepository;
use MetaFox\User\Repositories\Eloquent\PasswordResetTokenRepository;
use MetaFox\User\Repositories\Eloquent\SocialAccountRepository;
use MetaFox\User\Repositories\Eloquent\UserAdminRepository;
use MetaFox\User\Repositories\Eloquent\UserGenderRepository;
use MetaFox\User\Repositories\Eloquent\UserPrivacyRepository;
use MetaFox\User\Repositories\Eloquent\UserPromotionRepository;
use MetaFox\User\Repositories\Eloquent\UserRelationDataRepository;
use MetaFox\User\Repositories\Eloquent\UserRelationRepository;
use MetaFox\User\Repositories\Eloquent\UserRepository;
use MetaFox\User\Repositories\Eloquent\UserShortcutRepository;
use MetaFox\User\Repositories\Eloquent\UserVerifyErrorRepository;
use MetaFox\User\Repositories\Eloquent\UserVerifyRepository;
use MetaFox\User\Repositories\MultiFactorTokenRepositoryInterface;
use MetaFox\User\Repositories\PasswordResetTokenRepositoryInterface;
use MetaFox\User\Repositories\SocialAccountRepositoryInterface;
use MetaFox\User\Repositories\UserAdminRepositoryInterface;
use MetaFox\User\Repositories\UserGenderRepositoryInterface;
use MetaFox\User\Repositories\UserPrivacyRepositoryInterface;
use MetaFox\User\Repositories\UserPromotionRepositoryInterface;
use MetaFox\User\Repositories\UserRelationDataRepositoryInterface;
use MetaFox\User\Repositories\UserRelationRepositoryInterface;
use MetaFox\User\Repositories\UserShortcutRepositoryInterface;
use MetaFox\User\Repositories\UserVerifyErrorRepositoryInterface;
use MetaFox\User\Repositories\UserVerifyRepositoryInterface;
use MetaFox\User\Support\Facades\UserAuth;
use MetaFox\User\Support\UserBlockedSupport;
use MetaFox\User\Support\UserEntity;
use MetaFox\User\Support\UserPrivacy;
use MetaFox\User\Support\UserValue;

/**
 * Class UserServiceProvider.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * @var array<string, string>
     */
    public array $bindings = [
        UserVerifyRepositoryInterface::class  => UserVerifyRepository::class,
        AdminLoggedRepositoryInterface::class => AdminLoggedRepository::class,
    ];

    /**
     * @var array<string, string>
     */
    public array $singletons = [
        'user.verification' => UserVerifyRepository::class,
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            User::ENTITY_TYPE             => User::class,
            UserShortcut::ENTITY_TYPE     => UserShortcut::class,
            UserBan::ENTITY_TYPE          => UserBan::class,
            UserPrivacyModel::ENTITY_TYPE => UserPrivacyModel::class,
        ]);

        User::observe([UserObserver::class, EloquentModelObserver::class]);
        \MetaFox\User\Models\UserEntity::observe([UserEntityObserver::class]);
        UserBlocked::observe([EloquentModelObserver::class]);
        UserProfile::observe([UserProfileObserver::class, EloquentModelObserver::class]);
        UserBan::observe([UserBanObserver::class]);
        Role::observe([UserRoleObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Boot facades.
        $this->app->bind(UserAdminRepositoryInterface::class, UserAdminRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(SocialAccountRepositoryInterface::class, SocialAccountRepository::class);
        $this->app->bind(UserPrivacyRepositoryInterface::class, UserPrivacyRepository::class);
        $this->app->bind(CancelReasonRepositoryInterface::class, CancelReasonRepository::class);
        $this->app->bind(CancelFeedbackAdminRepositoryInterface::class, CancelFeedbackAdminRepository::class);
        $this->app->bind(UserPromotionRepositoryInterface::class, UserPromotionRepository::class);
        $this->app->bind(UserRelationRepositoryInterface::class, UserRelationRepository::class);
        $this->app->bind(UserShortcutRepositoryInterface::class, UserShortcutRepository::class);
        $this->app->bind('UserEntity', UserEntity::class);
        $this->app->bind('UserPrivacy', UserPrivacy::class);
        $this->app->bind('UserValue', UserValue::class);
        $this->app->bind(MultiFactorTokenRepositoryInterface::class, MultiFactorTokenRepository::class);
        $this->app->bind(UserRelationDataRepositoryInterface::class, UserRelationDataRepository::class);
        $this->app->bind(UserVerifyErrorRepositoryInterface::class, UserVerifyErrorRepository::class);
        $this->app->bind(UserGenderRepositoryInterface::class, UserGenderRepository::class);
        $this->app->bind(PasswordResetTokenRepositoryInterface::class, PasswordResetTokenRepository::class);

        $this->app->singleton(UserBlockedSupportContract::class, UserBlockedSupport::class);
        $this->app->singleton(UserContract::class, \MetaFox\User\Support\User::class);
        $this->app->singleton(ContractsUserAuth::class, UserAuth::class);
        $this->app->singleton(PermissionRegistrar::class, \MetaFox\User\Support\PermissionRegistrar::class);
    }
}
