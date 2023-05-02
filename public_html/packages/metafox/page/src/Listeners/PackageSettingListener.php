<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Page\Listeners;

use Illuminate\Console\Scheduling\Schedule;
use MetaFox\Page\Jobs\CleanUpDeletedPageJob;
use MetaFox\Page\Models\Category;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Models\PageMember;
use MetaFox\Page\Notifications\ApproveNewPostNotification;
use MetaFox\Page\Notifications\ApproveRequestClaimNotification;
use MetaFox\Page\Notifications\AssignOwnerNotification;
use MetaFox\Page\Notifications\ClaimNotification;
use MetaFox\Page\Notifications\LikePageNotification;
use MetaFox\Page\Notifications\PageApproveNotification;
use MetaFox\Page\Notifications\PageInvite as PageInviteNotification;
use MetaFox\Page\Policies\CategoryPolicy;
use MetaFox\Page\Policies\PageMemberPolicy;
use MetaFox\Page\Policies\PagePolicy;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxDataType;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;

/**
 * Class PackageSettingListener.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getActivityTypes(): array
    {
        return [
            [
                'type'            => Page::ENTITY_TYPE,
                'handler'         => ClaimNotification::class,
                'entity_type'     => Page::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'page::phrase.page_type',
                'description'     => 'page::phrase.added_a_page',
                'is_system'       => 0,
                'can_comment'     => false,
                'can_like'        => false,
                'can_share'       => true,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => false,
            ],
            [
                'type'            => Page::PAGE_UPDATE_PROFILE_ENTITY_TYPE,
                'entity_type'     => Page::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'page::phrase.page_type',
                'description'     => 'page_user_name_updated_their_profile_photo',
                'is_system'       => 0,
                'can_comment'     => true,
                'can_like'        => true,
                'can_share'       => true,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => true,
            ],
            [
                'type'            => Page::PAGE_UPDATE_COVER_ENTITY_TYPE,
                'entity_type'     => Page::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'page::phrase.page_type',
                'description'     => 'page_user_name_updated_their_cover_photo',
                'is_system'       => 0,
                'can_comment'     => true,
                'can_like'        => true,
                'can_share'       => true,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => true,
            ],
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            Page::ENTITY_TYPE => [
                'view'             => UserRole::LEVEL_GUEST,
                'create'           => UserRole::LEVEL_REGISTERED,
                'update'           => UserRole::LEVEL_REGISTERED,
                'delete'           => UserRole::LEVEL_REGISTERED,
                'moderate'         => UserRole::LEVEL_STAFF,
                'feature'          => UserRole::LEVEL_STAFF,
                'approve'          => UserRole::LEVEL_STAFF,
                'claim'            => UserRole::LEVEL_STAFF,
                'share'            => UserRole::LEVEL_REGISTERED,
                'report'           => UserRole::LEVEL_REGISTERED,
                'auto_approved'    => UserRole::LEVEL_REGISTERED,
                'upload_cover'     => UserRole::LEVEL_REGISTERED,
                'purchase_sponsor' => UserRole::LEVEL_REGISTERED,
                'sponsor'          => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    public function getUserValuePermissions(): array
    {
        return [
            Page::ENTITY_TYPE => [
                'flood_control' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => 0,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 0,
                        UserRole::STAFF_USER  => 0,
                        UserRole::NORMAL_USER => 0,
                    ],
                ],
                'quota_control' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => 0,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 0,
                        UserRole::STAFF_USER  => 0,
                        UserRole::NORMAL_USER => 0,
                    ],
                ],
                'purchase_sponsor_price' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => 0,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 0,
                        UserRole::STAFF_USER  => 0,
                        UserRole::NORMAL_USER => 0,
                    ],
                ],
            ],
        ];
    }

    public function getUserPrivacyResource(): array
    {
        return [
            Page::ENTITY_TYPE => [
                'core.view_publish_date',
                'core.view_admins' => [
                    'phrase' => 'page::phrase.user_privacy.who_can_view_admins',
                ],
            ],
        ];
    }

    public function getProfileMenu(): array
    {
        return [
            Page::ENTITY_TYPE => [
                'phrase'  => 'page::phrase.pages',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getEvents(): array
    {
        return [
            'models.notify.deleted' => [
                ModelDeletedListener::class,
            ],
            'models.notify.updated' => [
                ModelUpdatedListener::class,
            ],
            'page.update_cover' => [
                UpdatePageCover::class,
            ],
            'page.update_avatar' => [
                UpdatePageAvatar::class,
            ],
            'page.get_user_preview' => [
                UserPreviewListener::class,
            ],
            'page.get_search_resource' => [
                GetSearchResourceListener::class,
            ],
            'page.get_privacy_for_setting' => [
                PrivacyForSetting::class,
            ],
            'user.get_shortcut_type' => [
                GetShortcutTypeListener::class,
            ],
            'parseRoute' => [
                PageRouteListener::class,
            ],
            'friend.mention.builder' => [
                FriendMentionBuilderListener::class,
            ],
            'core.parse_content' => [
                ParseFeedContentListener::class,
            ],
            'friend.mention.members.builder' => [
                FriendMemberMentionBuilderListener::class,
            ],
            'friend.mention.extra_info' => [
                FriendMentionExtraInfoListener::class,
            ],
            'friend.invite.members' => [
                GetIdsUserInviteListener::class,
            ],
            'activity.share.data_preparation' => [
                SharedDataPreparationListener::class,
            ],
            'activity.share.rules' => [
                ShareRuleListener::class,
            ],
            'search.owner_options' => [
                SearchOwnerOptionListener::class,
            ],
            'core.collect_total_items_stat' => [
                CollectTotalItemsStatListener::class,
            ],
            'activity.get_privacy_detail_on_owner' => [
                GetPrivacyDetailOnOwnerListener::class,
            ],
            'friend.mention.notifiables' => [
                FriendMentionNotifiableListener::class,
            ],
            'user.role.downgrade' => [
                UserRoleDowngradeListener::class,
            ],
            'activity.notify.approved_new_post_in_owner' => [
                ApprovedNewPostListener::class,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'default_item_privacy'                 => ['value' => MetaFoxPrivacy::EVERYONE],
            'admin_in_charge_of_page_claims'       => ['value' => 0],
            'display_profile_photo_within_gallery' => ['value' => true],
            'display_cover_photo_within_gallery'   => ['value' => true],
            'default_category'                     => ['value' => 1],
            'minimum_name_length'                  => ['value' => MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH],
            'maximum_name_length'                  => ['value' => 64],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'page_invite',
                'module_id'  => 'page',
                'handler'    => PageInviteNotification::class,
                'title'      => 'page::phrase.page_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 15,
            ],
            [
                'type'       => 'claim_page',
                'module_id'  => 'page',
                'handler'    => ClaimNotification::class,
                'title'      => 'page::phrase.claim_page_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 0,
                'channels'   => ['database', 'mail', 'sms', 'webpush'],
                'ordering'   => 16,
            ],
            [
                'type'       => 'like_page',
                'module_id'  => 'page',
                'handler'    => LikePageNotification::class,
                'title'      => 'page::phrase.like_page_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 0,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 17,
            ],
            [
                'type'       => 'page_approve_notification',
                'module_id'  => 'page',
                'handler'    => PageApproveNotification::class,
                'title'      => 'page::phrase.page_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'sms', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 18,
            ],
            [
                'type'       => 'approve_claim_page',
                'module_id'  => 'page',
                'handler'    => ApproveRequestClaimNotification::class,
                'title'      => 'page::phrase.approve_claim_page_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'sms', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 19,
            ],
            [
                'type'       => 'page_new_post',
                'module_id'  => 'page',
                'handler'    => ApproveNewPostNotification::class,
                'title'      => 'page::phrase.page_new_post_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'database', 'mobilepush', 'webpush'],
            ],
            [
                'type'       => 'assign_owner_page',
                'module_id'  => 'page',
                'handler'    => AssignOwnerNotification::class,
                'title'      => 'page::phrase.assign_owner_page_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'sms', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 20,
            ],
        ];
    }

    public function getPolicies(): array
    {
        return [
            Page::class       => PagePolicy::class,
            Category::class   => CategoryPolicy::class,
            PageMember::class => PageMemberPolicy::class,
        ];
    }

    public function getSiteStatContent(): ?array
    {
        return [
            Page::ENTITY_TYPE => 'ico-flag-waving-o',
        ];
    }

    public function registerApplicationSchedule(Schedule $schedule): void
    {
        $schedule->job(resolve(CleanUpDeletedPageJob::class))->everySixHours()->withoutOverlapping();
    }

    /**
     * @return array<string>
     */
    public function getSitemap(): array
    {
        return ['page', 'page_category'];
    }

    /**
     * @return array<int, mixed>
     */
    public function getAdMobPages(): array
    {
        return [
            [
                'path' => '/page',
                'name' => 'page::phrase.ad_mob_page_home_page',
            ],
            [
                'path' => '/page/:id',
                'name' => 'page::phrase.ad_mob_page_detail_page',
            ],
        ];
    }
}
