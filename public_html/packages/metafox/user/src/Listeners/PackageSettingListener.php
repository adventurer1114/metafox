<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Listeners;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Carbon;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Events\RefreshTokenCreated;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;
use MetaFox\User\Jobs\CleanUpDeletedUserJob;
use MetaFox\User\Jobs\ExpiredUserBanJob;
use MetaFox\User\Jobs\MaintainPendingVerification;
use MetaFox\User\Models\User;
use MetaFox\User\Models\UserProfile;
use MetaFox\User\Models\UserShortcut;
use MetaFox\User\Notifications\DirectUpdatedPassword;
use MetaFox\User\Notifications\NewPostTimeline;
use MetaFox\User\Notifications\ProfileUpdatedByAdmin;
use MetaFox\User\Notifications\ResetPasswordTokenNotification;
use MetaFox\User\Notifications\UserApproveNotification;
use MetaFox\User\Notifications\VerifyEmail;
use MetaFox\User\Notifications\WelcomeNewMember;
use MetaFox\User\Policies\UserPolicy;
use MetaFox\User\Policies\UserProfilePolicy;
use MetaFox\User\Policies\UserShortcutPolicy;
use MetaFox\User\Support\Browse\Scopes\User\SortScope;
use MetaFox\User\Support\User as UserSupport;

/**
 * Class PackageSettingListener.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @ignore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    /**
     * @return string[]
     */
    public function getCaptchaRules(): array
    {
        return [
            'user_signup',
            'user_login',
            'forgot_password',
        ];
    }

    public function getActivityTypes(): array
    {
        return [
            [
                'type'            => User::ENTITY_TYPE,
                'entity_type'     => User::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'user::phrase.user_registered_type',
                'description'     => 'user::phrase.has_registered',
                'is_system'       => 0,
                'can_comment'     => false,
                'can_like'        => false,
                'can_share'       => false,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => true,
            ],
            [
                'type'            => User::USER_UPDATE_AVATAR_ENTITY_TYPE,
                'entity_type'     => User::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'user::phrase.user_update_avatar_type',
                'description'     => 'user::phrase.user_name_updated_their_profile_picture',
                'is_system'       => 0,
                'can_comment'     => true,
                'can_like'        => true,
                'can_share'       => false,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => true,
                'params'          => [
                    'gender' => 'user_entity.possessive_gender',
                ],
            ],
            [
                'type'            => User::USER_UPDATE_COVER_ENTITY_TYPE,
                'entity_type'     => User::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'user::phrase.user_update_cover_photo_type',
                'description'     => 'user::phrase.user_name_updated_their_cover_photo',
                'is_system'       => 0,
                'can_comment'     => true,
                'can_like'        => true,
                'can_share'       => true,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => true,
                'params'          => [
                    'gender' => 'user_entity.possessive_gender',
                ],
            ],
            [
                'type'            => User::USER_AVATAR_SIGN_UP,
                'entity_type'     => User::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'user::phrase.user_upload_signup_avatar_type',
                'description'     => '',
                'is_system'       => 0,
                'can_comment'     => false,
                'can_like'        => false,
                'can_share'       => false,
                'can_edit'        => false,
                'can_create_feed' => false,
                'can_put_stream'  => false,
            ],
            [
                'type'            => User::USER_UPDATE_INFORMATION_ENTITY_TYPE,
                'entity_type'     => User::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'user::phrase.user_update_information_type',
                'description'     => 'user::phrase.user_name_updated_their_information',
                'is_system'       => 0,
                'can_comment'     => false,
                'can_like'        => true,
                'can_share'       => true,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => true,
                'action_on_feed'  => true,
                'params'          => [
                    'gender' => 'user_entity.possessive_gender',
                ],
            ],
            [
                'type'            => User::USER_UPDATE_RELATIONSHIP_ENTITY_TYPE,
                'entity_type'     => UserProfile::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'user::phrase.user_update_relationship_type',
                'description'     => 'user_name_updated_their_relationship',
                'is_system'       => 0,
                'can_comment'     => true,
                'can_like'        => true,
                'can_share'       => false,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => true,
                'action_on_feed'  => true,
            ],
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            User::ENTITY_TYPE => [
                'view'                      => UserRole::LEVEL_GUEST,
                'update'                    => UserRole::LEVEL_REGISTERED,
                'delete'                    => UserRole::LEVEL_REGISTERED,
                'moderate'                  => UserRole::LEVEL_STAFF,
                'can_block_other_members'   => UserRole::LEVEL_REGISTERED,
                'can_be_blocked_by_others'  => [UserRole::LEVEL_REGISTERED],
                'report'                    => UserRole::LEVEL_REGISTERED,
                'feature'                   => UserRole::LEVEL_STAFF,
                'can_override_user_privacy' => UserRole::LEVEL_ADMINISTRATOR,
            ],
            UserShortcut::ENTITY_TYPE => [
                'view'     => UserRole::LEVEL_REGISTERED,
                'moderate' => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    public function getPolicies(): array
    {
        return [
            User::class         => UserPolicy::class,
            UserShortcut::class => UserShortcutPolicy::class,
            UserProfile::class  => UserProfilePolicy::class,
        ];
    }

    public function getEvents(): array
    {
        return [
            'packages.installed'    => [PackageInstalledListener::class],
            'models.notify.created' => [ModelCreatedListener::class],
            'models.notify.deleted' => [ModelDeletedListener::class],
            'models.notify.updated' => [ModelUpdatedListener::class],
            'user.get_user_preview' => [
                UserPreviewListener::class,
            ],
            'user.get_mentions' => [
                UserGetMentions::class,
            ],
            AccessTokenCreated::class => [
                AccessTokenCreatedListener::class,
            ],
            RefreshTokenCreated::class => [
                RefreshTokenCreatedListener::class,
            ],
            'user.get_search_resource' => [
                GetSearchResourceListener::class,
            ],
            'user.get_privacy_for_setting' => [
                PrivacyForSetting::class,
            ],
            'parseRoute' => [
                ProfileRouteListener::class,
                SettingRouteListener::class,
            ],
            'feed.composer.notification' => [
                FeedComposerNotificationListener::class,
            ],
            'user.update_cover' => [
                UpdateProfileCoverListener::class,
            ],
            'user.update_avatar' => [
                UpdateProfileAvatarListener::class,
            ],
            'user.user_blocked' => [
                BlockedListener::class,
            ],
            'user.user_unblocked' => [
                UnBlockedListener::class,
            ],
            'user.check_value_setting_by_name' => [
                CheckUserValueSettingByNameListener::class,
            ],
            'user.permissions.extra' => [
                UserExtraPermissionListener::class,
            ],
            'friend.mention.extra_info' => [
                FriendMentionExtraInfoListener::class,
            ],
            'user.registration.extra_field.rules' => [
                UserRegistrationExtraFieldsRulesListener::class,
            ],
            'user.registration.extra_field.create' => [
                UserRegistrationExtraFieldsCreateListener::class,
            ],
            'search.owner_options' => [
                SearchOwnerOptionListener::class,
            ],
            'user.registered' => [
                UserRegisteredListener::class,
            ],
            'user.verified' => [
                UserVerifiedListener::class,
            ],
            'validation.unique_slug' => [
                UniqueSlugListener::class,
            ],
            'user.signed_in' => [
                UserSignedInListener::class,
            ],
            'core.collect_total_items_stat' => [
                CollectTotalItemsStatListener::class,
            ],
            'like.owner.notification' => [
                LikeNotificationListener::class,
            ],
            'user.deleting' => [
                UserDeletingListener::class,
            ],
            'user.logout' => [
                UserLogoutListener::class,
            ],
        ];
    }

    public function getUserPrivacy(): array
    {
        return [
            'profile.view_profile' => [
                'phrase'  => 'user::phrase.user_privacy.who_can_view_your_profile_page',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
            'profile.profile_info' => [
                'phrase'  => 'user::phrase.user_privacy.who_can_view_the_info_tab_on_your_profile_page',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
            'profile.basic_info' => [
                'phrase'  => 'user::phrase.user_privacy.who_can_view_your_basic_info',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
            'profile.view_location' => [
                'phrase'  => 'user::phrase.user_privacy.who_can_view_your_location',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
            'user.can_i_be_tagged' => [
                'phrase'  => 'user::phrase.user_privacy.who_can_tag_me_in_written_context',
                'default' => MetaFoxPrivacy::FRIENDS,
            ],
        ];
    }

    public function getUserPrivacyResource(): array
    {
        return [
            User::ENTITY_TYPE => [
                'profile.view_profile',
                'profile.profile_info',
                'profile.basic_info',
                'profile.view_location',
                'user.can_i_be_tagged' => [
                    'default' => MetaFoxPrivacy::FRIENDS,
                    'list'    => [
                        MetaFoxPrivacy::FRIENDS,
                        MetaFoxPrivacy::ONLY_ME,
                    ],
                ],
            ],
        ];
    }

    public function registerApplicationSchedule(Schedule $schedule): void
    {
        $schedule->job(resolve(ExpiredUserBanJob::class))->hourly()->withoutOverlapping();
        $schedule->job(resolve(MaintainPendingVerification::class))->hourly()->withoutOverlapping();
        $schedule->job(resolve(CleanUpDeletedUserJob::class))->everySixHours()->withoutOverlapping();
    }

    public function getSiteSettings(): array
    {
        return [
            'on_register_user_group'  => ['value' => UserRole::NORMAL_USER_ID],
            'allow_user_registration' => ['value' => true],
            'signup_repeat_password'  => ['value' => false],
            // 'multi_step_registration_form'               => ['value' => false],
            'new_user_terms_confirmation' => ['value' => true],
            // 'logout_after_change_phone_number'           => ['value' => true],
            // 'logout_after_change_email_if_verify'        => ['value' => true],
            // 'display_user_online_status'                 => ['value' => false],
            // 'profile_use_id'                             => ['value' => false],
            // 'login_type'                                 => ['value' => 'email'],
            'send_welcome_email'  => ['value' => 1],
            'date_of_birth_start' => ['value' => 1900],
            'date_of_birth_end'   => ['value' => Carbon::now()->year],
            // 'display_or_full_name'                       => ['value' => 'full_name'],
            // 'check_promotion_system'                     => ['value' => true],
            // 'enable_user_tooltip'                        => ['value' => true],
            // 'brute.force_attempts_count'                 => ['value' => 5],
            // 'brute.force_time_check'                     => ['value' => 0],
            // 'brute.brute_force_cool_down'                => ['value' => 15],
            'enable_relationship_status'                => ['value' => true],
            'enable_date_of_birth'                      => ['value' => false],
            'enable_gender'                             => ['value' => false],
            'enable_location'                           => ['value' => false],
            'enable_city'                               => ['value' => false],
            'verify_email_at_signup'                    => ['value' => false],
            'verify_email_timeout'                      => ['value' => 60],
            'days_for_delete_pending_user_verification' => ['value' => 0],
            'resend_verification_email_delay_time'      => ['value' => 15],
            'maximum_length_for_full_name'              => ['value' => 25],
            'minimum_length_for_password'               => ['value' => 4],
            'maximum_length_for_password'               => ['value' => 30],
            'default_birthday_privacy'                  => ['value' => UserSupport::DATE_OF_BIRTH_SHOW_ALL],
            'user_dob_month_day_year'                   => 'F j, Y',
            'user_dob_month_day'                        => 'F j',
            // 'split_full_name'                            => ['value' => false],
            'redirect_after_login' => ['value' => ''],
            // 'redirect_after_signup'                      => ['value' => ''],
            'redirect_after_logout' => ['value' => ''],
            // 'disable_store_last_user'                    => ['value' => false],
            'enable_feed_user_update_relationship' => ['value' => true],
            // 'cache_recent_logged_in'                     => ['value' => 0],
            'min_length_for_username'         => ['value' => 5],
            'max_length_for_username'         => ['value' => 25],
            'enable_feed_user_update_profile' => ['value' => false],
            // 'validate_full_name'                         => ['value' => true],
            'approve_users'                   => ['value' => false],
            'force_user_to_upload_on_sign_up' => ['value' => false],
            'on_signup_new_friend'            => ['value' => ''],
            'redirect_after_signup'           => ['value' => ''],
            'on_register_privacy_setting'     => ['value' => MetaFoxPrivacy::MEMBERS],
            // 'disable_username_on_sign_up'                => ['value' => 'full_name'],
            'captcha_on_login' => ['value' => false],
            // 'hide_main_menu'                             => ['value' => false],
            // 'invite_only_community'                      => ['value' => false],
            'require_basic_field'            => ['value' => false],
            'required_strong_password'       => ['value' => false],
            'browse_user_default_order'      => ['value' => SortScope::SORT_FULL_NAME],
            'force_user_to_reenter_email'    => ['value' => false],
            'shorter_reset_password_routine' => ['value' => false],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'request_reset_password_token',
                'title'      => 'user::phrase.new_password_requested_notification_type',
                'handler'    => ResetPasswordTokenNotification::class,
                'module_id'  => 'user',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail'],
                'ordering'   => 20,
            ],
            [
                'type'       => 'new_password_updated',
                'title'      => 'user::phrase.new_password_updated_notification_type',
                'handler'    => DirectUpdatedPassword::class,
                'module_id'  => 'user',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail'],
                'ordering'   => 21,
            ],
            [
                'type'       => 'new_post_timeline',
                'title'      => 'user::phrase.new_post_timeline_notification_type',
                'handler'    => NewPostTimeline::class,
                'module_id'  => 'user',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 22,
            ],
            [
                'type'       => 'profile_updated_by_admin',
                'title'      => 'user::phrase.profile_updated_by_admin_notification_type',
                'handler'    => ProfileUpdatedByAdmin::class,
                'module_id'  => 'user',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 23,
            ],
            [
                'type'       => 'user_approve_notification',
                'module_id'  => 'user',
                'handler'    => UserApproveNotification::class,
                'title'      => 'user::phrase.user_approved_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail'],
                'ordering'   => 24,
            ],
            [
                'type'       => 'user_verify_email_signup',
                'module_id'  => 'user',
                'handler'    => VerifyEmail::class,
                'title'      => 'user::phrase.user_verify_email_signup_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 0,
                'channels'   => ['mail'],
                'ordering'   => 25,
            ],
            [
                'type'       => 'user_welcome',
                'module_id'  => 'user',
                'handler'    => WelcomeNewMember::class,
                'title'      => 'user::phrase.user_welcome_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 0,
                'channels'   => ['mail'],
                'ordering'   => 26,
            ],
            [
                // For removing obsolete notification type
                'type'       => 'new_password_requested',
                'is_deleted' => 1,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getUserValues(): array
    {
        return [
            User::ENTITY_TYPE => [
                'user_profile_date_of_birth_format' => [
                    'default_value' => UserSupport::DATE_OF_BIRTH_SHOW_ALL,
                    'ordering'      => 1,
                ],
                'user_auto_add_tagger_post' => [
                    'default_value' => UserSupport::AUTO_APPROVED_TAGGER_POST,
                    'ordering'      => 1,
                ],
                'announcement_close' => [
                    'default_value' => 0,
                    'ordering'      => 1,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getActivityPointSettings(): array
    {
        return [
            'metafox/user' => [
                [
                    'name'               => User::ENTITY_TYPE . '.sign_up',
                    'action'             => 'sign_up',
                    'module_id'          => 'user',
                    'package_id'         => 'metafox/user',
                    'description_phrase' => 'user::activitypoint.setting_sign_up_description',
                    'extra'              => [
                        'disabled' => ['max_earned', 'period'],
                    ],
                ],
                [
                    'name'               => User::ENTITY_TYPE . '.sign_in',
                    'action'             => 'sign_in',
                    'module_id'          => 'user',
                    'package_id'         => 'metafox/user',
                    'description_phrase' => 'user::activitypoint.setting_sign_in_description',
                ],
                [
                    'name'               => User::ENTITY_TYPE . '.new_profile_photo',
                    'action'             => 'new_profile_photo',
                    'module_id'          => 'user',
                    'package_id'         => 'metafox/user',
                    'description_phrase' => 'user::activitypoint.setting_new_profile_photo_description',
                ],
                [
                    'name'               => User::ENTITY_TYPE . '.new_profile_cover',
                    'action'             => 'new_profile_cover',
                    'module_id'          => 'user',
                    'package_id'         => 'metafox/user',
                    'description_phrase' => 'user::activitypoint.setting_new_profile_cover_description',
                ],
            ],
        ];
    }

    /**
     * @return string[]|null
     */
    public function getSiteStatContent(): ?array
    {
        return [
            User::ENTITY_TYPE => 'ico-user1-three',
            'online_user'     => 'ico-user1-check',
            'pending_user'    => 'ico-user1-clock',
        ];
    }

    /**
     * @return array<string>
     */
    public function getSitemap(): array
    {
        return ['user'];
    }

    /**
     * @return array<int, mixed>
     */
    public function getAdMobPages(): array
    {
        return [
            [
                'path' => '/user',
                'name' => 'user::phrase.ad_mob_home_page',
            ],
            [
                'path' => '/user/:id',
                'name' => 'user::phrase.ad_mob_profile_page',
            ],
        ];
    }
}
