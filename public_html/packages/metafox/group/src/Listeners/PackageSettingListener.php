<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Group\Listeners;

use Illuminate\Console\Scheduling\Schedule;
use MetaFox\Group\Http\Resources\v1\WebSetting;
use MetaFox\Group\Jobs\ChangePrivacyGroupJob;
use MetaFox\Group\Jobs\CleanUpDeletedGroupJob;
use MetaFox\Group\Jobs\UnmuteInGroupJob;
use MetaFox\Group\Jobs\UpdateStatusCodeInviteJob;
use MetaFox\Group\Models\Category;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Models\Member;
use MetaFox\Group\Models\Question;
use MetaFox\Group\Notifications\AcceptRequestNotification;
use MetaFox\Group\Notifications\AddGroupAdmin as AddGroupAdminNotification;
use MetaFox\Group\Notifications\AddGroupModerator;
use MetaFox\Group\Notifications\ApproveNewPostNotification;
use MetaFox\Group\Notifications\AssignOwnerGroupNotification;
use MetaFox\Group\Notifications\GroupApproveNotification;
use MetaFox\Group\Notifications\GroupInvite as GroupInviteNotification;
use MetaFox\Group\Notifications\PendingPrivacyNotification;
use MetaFox\Group\Notifications\PendingRequestNotification;
use MetaFox\Group\Notifications\SuccessPrivacyNotification;
use MetaFox\Group\Policies\CategoryPolicy;
use MetaFox\Group\Policies\GroupPolicy;
use MetaFox\Group\Policies\MemberPolicy;
use MetaFox\Platform\MetaFoxDataType;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;

/**
 * Class PackageSettingListener.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getActivityTypes(): array
    {
        return [
            [
                'type'            => Group::ENTITY_TYPE,
                'entity_type'     => Group::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'group::phrase.group_type',
                'description'     => null,
                'is_system'       => 0,
                'can_comment'     => false,
                'can_like'        => false,
                'can_share'       => true,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => false,
            ],
            [
                'type'            => Group::GROUP_UPDATE_COVER_ENTITY_TYPE,
                'entity_type'     => Group::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'group::phrase.group_type',
                'description'     => 'user_name_updated_their_cover_photo',
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

    public function getEvents(): array
    {
        return [
            'models.notify.deleted' => [
                ModelDeletedListener::class,
            ],
            'models.notify.updated' => [
                ModelUpdatedListener::class,
            ],
            'group.update_cover' => [
                UpdateGroupCover::class,
            ],
            'group.get_user_preview' => [
                UserPreviewListener::class,
            ],
            'group.get_search_resource' => [
                GetSearchResourceListener::class,
            ],
            'group.get_privacy_for_setting' => [
                PrivacyForSetting::class,
            ],
            'user.get_shortcut_type' => [
                GetShortcutTypeListener::class,
            ],
            'friend.mention.members' => [
                MemberMentionListener::class,
            ],
            'friend.invite.members' => [
                GetIdsUserInviteListener::class,
            ],
            'activity.feed.collection_icons' => [
                CollectIconListener::class,
            ],
            'parseRoute' => [
                GroupRouteListener::class,
            ],
            'friend.mention.builder' => [
                FriendMentionBuilderListener::class,
            ],
            'friend.mention.notifiables' => [
                FriendMentionNotifiableListener::class,
            ],
            'user.get_mentions' => [
                UserGetMentionsListener::class,
            ],
            'core.parse_content' => [
                ParseFeedContentListener::class,
            ],
            'friend.mention.members.builder' => [
                FriendMemberMentionBuilderListener::class,
            ],
            'activity.feed.block_author' => [
                BlockAuthorListener::class,
            ],
            'activity.notify.approved_new_post_in_owner' => [
                ApprovedNewPostListener::class,
            ],
            'friend.mention.extra_info' => [
                FriendMentionExtraInfoListener::class,
            ],
            'activity.share.data_preparation' => [
                SharedDataPreparationListener::class,
            ],
            'activity.share.rules' => [
                ShareRuleListener::class,
            ],
            'friend.invite.members.builder' => [
                FriendInviteMemberBuilderListener::class,
            ],
            'search.owner_options' => [
                SearchOwnerOptionListener::class,
            ],
            'activity.get_privacy_detail_on_owner' => [
                GetPrivacyDetailOnOwnerListener::class,
            ],
            'comment.owner.notification' => [
                CommentNotificationListener::class,
            ],
            'like.owner.notification' => [
                LikeNotificationListener::class,
            ],
            'core.collect_total_items_stat' => [
                CollectTotalItemsStatListener::class,
            ],
            'feed.permissions.extra' => [
                FeedExtraPermissionListener::class,
            ],
            'group.announcement_deleted' => [
                AnnouncementDeletedListener::class,
            ],
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            Member::ENTITY_TYPE => [
                'view' => UserRole::LEVEL_GUEST,
            ],
            Group::ENTITY_TYPE => [
                'view'             => UserRole::LEVEL_GUEST,
                'create'           => UserRole::LEVEL_REGISTERED,
                'update'           => UserRole::LEVEL_REGISTERED,
                'delete'           => UserRole::LEVEL_REGISTERED,
                'moderate'         => UserRole::LEVEL_ADMINISTRATOR,
                'feature'          => UserRole::LEVEL_STAFF,
                'approve'          => UserRole::LEVEL_STAFF,
                'report'           => UserRole::LEVEL_REGISTERED,
                'purchase_sponsor' => UserRole::LEVEL_STAFF,
                'sponsor'          => UserRole::LEVEL_REGISTERED,
                'auto_approved'    => UserRole::LEVEL_REGISTERED,
                'upload_cover'     => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    public function getUserPrivacy(): array
    {
        return [];
    }

    public function getUserPrivacyResource(): array
    {
        return [
            Group::ENTITY_TYPE => [
                'core.view_admins' => [
                    'phrase' => 'group::phrase.user_privacy.who_can_view_admins',
                ],
                'core.view_publish_date' => [
                    'phrase' => 'group::phrase.user_privacy.who_can_view_group_s_publish_date',
                ],
            ],
        ];
    }

    public function getProfileMenu(): array
    {
        return [
            Group::ENTITY_TYPE => [
                'phrase'  => 'group::phrase.groups',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'display_cover_photo_within_gallery'    => ['value' => true],
            'default_item_privacy'                  => ['value' => MetaFoxPrivacy::FRIENDS],
            'minimum_name_length'                   => ['value' => 3],
            'maximum_name_length'                   => ['value' => 64],
            'maximum_membership_question'           => ['value' => Question::MAX_QUESTION],
            'maximum_membership_question_option'    => ['value' => 5],
            'maximum_number_group_rule'             => ['value' => 3],
            'time_muted_member_option'              => ['value' => []],
            'number_days_expiration_change_privacy' => ['value' => 0],
            'number_hours_expiration_invite_code'   => ['value' => 0],
            'invite_expiration_role'                => ['value' => 0],
            'invite_expiration_interval'            => ['value' => 0],
            'default_category'                      => ['value' => 1],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'group_invite',
                'module_id'  => 'group',
                'handler'    => GroupInviteNotification::class,
                'title'      => 'group::phrase.group_invite_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 1,
            ],
            [
                'type'       => 'group_pending_request',
                'module_id'  => 'group',
                'handler'    => PendingRequestNotification::class,
                'title'      => 'group::phrase.group_pending_request_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 2,
            ],
            [
                'type'       => 'add_group_moderator',
                'module_id'  => 'group',
                'handler'    => AddGroupModerator::class,
                'title'      => 'group::phrase.group_add_moderator_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 3,
            ],
            [
                'type'       => 'add_group_admin',
                'module_id'  => 'group',
                'handler'    => AddGroupAdminNotification::class,
                'title'      => 'group::phrase.group_add_admin_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 4,
            ],
            [
                'type'       => 'accept_request_member',
                'module_id'  => 'group',
                'handler'    => AcceptRequestNotification::class,
                'title'      => 'group::phrase.group_accept_request_member_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 5,
            ],
            [
                'type'       => 'pending_privacy',
                'module_id'  => 'group',
                'handler'    => PendingPrivacyNotification::class,
                'title'      => 'group::phrase.pending_privacy_group_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 6,
            ],
            [
                'type'       => 'success_change_privacy',
                'module_id'  => 'group',
                'handler'    => SuccessPrivacyNotification::class,
                'title'      => 'group::phrase.success_privacy_group_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 7,
            ],
            [
                'type'       => 'approved_new_post',
                'module_id'  => 'group',
                'handler'    => ApproveNewPostNotification::class,
                'title'      => 'group::phrase.approved_new_post_in_group_notification',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 8,
            ],
            [
                'type'       => 'group_approve_notification',
                'module_id'  => 'group',
                'handler'    => GroupApproveNotification::class,
                'title'      => 'group::phrase.group_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'sms', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 18,
            ],
            [
                'type'       => 'assign_owner_notification',
                'module_id'  => 'group',
                'handler'    => AssignOwnerGroupNotification::class,
                'title'      => 'group::phrase.assign_owner_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'sms', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 19,
            ],
        ];
    }

    public function getPolicies(): array
    {
        return [
            Group::class    => GroupPolicy::class,
            Category::class => CategoryPolicy::class,
            Member::class   => MemberPolicy::class,
        ];
    }

    public function getUserValues(): array
    {
        return [
            Group::ENTITY_TYPE => [
                'approve_or_deny_membership_request' => [
                    'default_value' => true,
                    'ordering'      => 1,
                ],
                'approve_or_deny_post' => [
                    'default_value' => true,
                    'ordering'      => 2,
                ],
                'remove_post_and_comment_on_post' => [
                    'default_value' => true,
                    'ordering'      => 3,
                ],
                'remove_and_block_people_from_the_group' => [
                    'default_value' => true,
                    'ordering'      => 4,
                ],
            ],
        ];
    }

    public function getUserValuePermissions(): array
    {
        return [
            Group::ENTITY_TYPE => [
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

    public function registerApplicationSchedule(Schedule $schedule): void
    {
        $schedule->job(resolve(ChangePrivacyGroupJob::class))->daily();
        $schedule->job(resolve(UnmuteInGroupJob::class))->everyFiveMinutes();
        $schedule->job(resolve(UpdateStatusCodeInviteJob::class))->everyFiveMinutes();
        $schedule->job(resolve(CleanUpDeletedGroupJob::class))->everySixHours();
    }

    public function getSiteStatContent(): ?array
    {
        return [
            Group::ENTITY_TYPE => 'ico-user-man-three-o',
        ];
    }

    /**
     * @return array<string>
     */
    public function getSitemap(): array
    {
        return ['group', 'group_category'];
    }

    /**
     * @return array<int, mixed>
     */
    public function getAdMobPages(): array
    {
        return [
            [
                'path' => '/group',
                'name' => 'group::phrase.ad_mob_group_home_page',
            ],
            [
                'path' => '/group/:id',
                'name' => 'group::phrase.ad_mob_group_detail_page',
            ],
        ];
    }
}
