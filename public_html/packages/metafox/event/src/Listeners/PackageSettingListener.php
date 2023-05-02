<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Event\Listeners;

use Illuminate\Console\Scheduling\Schedule;
use MetaFox\Event\Jobs\CleanUpDeletedEventJob;
use MetaFox\Event\Jobs\UpdateStatusCodeInviteJob;
use MetaFox\Event\Models\Category;
use MetaFox\Event\Models\Event;
use MetaFox\Event\Models\Member;
use MetaFox\Event\Notifications\EventApproveNotifications;
use MetaFox\Event\Notifications\EventRsvpNotification;
use MetaFox\Event\Notifications\HostInvite as HostInviteNotification;
use MetaFox\Event\Notifications\Invite as InviteNotification;
use MetaFox\Event\Notifications\NewEventDiscussion;
use MetaFox\Event\Policies\CategoryPolicy;
use MetaFox\Event\Policies\EventPolicy;
use MetaFox\Event\Policies\MemberPolicy;
use MetaFox\Platform\MetaFoxDataType;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getPolicies(): array
    {
        return [
            Event::class    => EventPolicy::class,
            Category::class => CategoryPolicy::class,
            Member::class   => MemberPolicy::class,
        ];
    }

    public function getActivityTypes(): array
    {
        return [
            [
                'type'                   => Event::ENTITY_TYPE,
                'entity_type'            => Event::ENTITY_TYPE,
                'is_active'              => true,
                'title'                  => 'event::phrase.event_type',
                'description'            => 'added_an_event',
                'is_system'              => 0,
                'can_comment'            => true,
                'can_like'               => true,
                'can_share'              => true,
                'can_edit'               => false,
                'can_create_feed'        => true,
                'can_put_stream'         => true,
                'can_redirect_to_detail' => true,
            ],
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            Event::ENTITY_TYPE => [
                'view'             => UserRole::LEVEL_GUEST,
                'create'           => UserRole::LEVEL_REGISTERED,
                'update'           => UserRole::LEVEL_REGISTERED,
                'delete'           => UserRole::LEVEL_REGISTERED,
                'moderate'         => UserRole::LEVEL_STAFF,
                'feature'          => UserRole::LEVEL_REGISTERED,
                'approve'          => UserRole::LEVEL_STAFF,
                'save'             => UserRole::LEVEL_REGISTERED,
                'like'             => UserRole::LEVEL_REGISTERED,
                'share'            => UserRole::LEVEL_REGISTERED,
                'report'           => UserRole::LEVEL_REGISTERED,
                'purchase_sponsor' => UserRole::LEVEL_REGISTERED,
                'sponsor'          => UserRole::LEVEL_REGISTERED,
                'auto_approved'    => UserRole::LEVEL_REGISTERED,
                'discussion'       => UserRole::LEVEL_REGISTERED,
                'mass_email'       => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    public function getUserPrivacy(): array
    {
        return [
            'event.share_events' => [
                'phrase' => 'event::phrase.user_privacy.who_can_share_events',
            ],
            'event.view_browse_events' => [
                'phrase' => 'event::phrase.user_privacy.who_can_view_browse_events',
            ],
            'event.view_members' => [
                'phrase' => 'event::phrase.user_privacy.who_can_view_guests_list',
            ],
            'event.view_hosts' => [
                'phrase' => 'event::phrase.user_privacy.who_can_view_hosts_list',
            ],
            'event.manage_pending_post' => [
                'phrase' => 'event::phrase.user_privacy.who_can_approve_posts',
            ],
        ];
    }

    public function getUserPrivacyResource(): array
    {
        return [
            Event::ENTITY_TYPE => [
                'feed.share_on_wall' => [
                    'phrase'  => 'event::phrase.user_privacy.who_can_start_a_discussion',
                    'default' => MetaFoxPrivacy::FRIENDS,
                ],
                'feed.view_wall' => [
                    'phrase'  => 'event::phrase.user_privacy.who_can_view_discussions',
                    'default' => MetaFoxPrivacy::FRIENDS,
                ],
                'event.view_members' => [
                    'default' => MetaFoxPrivacy::EVERYONE,
                ],
                'event.view_hosts' => [
                    'default' => MetaFoxPrivacy::EVERYONE,
                ],
                'event.manage_pending_post' => [
                    'default' => MetaFoxPrivacy::CUSTOM,
                ],
            ],
            'page' => [
                'event.share_events',
                'event.view_browse_events',
            ],
            'group' => [
                'event.share_events',
            ],
        ];
    }

    public function getDefaultPrivacy(): array
    {
        return [
            Event::ENTITY_TYPE => [
                'phrase'  => 'event::phrase.events',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getProfileMenu(): array
    {
        return [
            Event::ENTITY_TYPE => [
                'phrase'  => 'event::phrase.events',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'minimum_name_length' => [
                'value' => 3,
            ],
            'maximum_name_length' => [
                'value' => 100,
            ],
            'default_category'                    => ['value' => 1],
            'number_hours_expiration_invite_code' => ['value' => 48],
            'invite_expiration_role'              => ['value' => 0],
            'default_time_format'                 => ['value' => 12],
            'enable_map'                          => ['value' => true],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'event_invite',
                'module_id'  => 'event',
                'handler'    => InviteNotification::class,
                'title'      => 'event::phrase.event_invite_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 13,
            ],
            [
                'type'       => 'event_host_invite',
                'module_id'  => 'event',
                'handler'    => HostInviteNotification::class,
                'title'      => 'event::phrase.event_host_invite_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 14,
            ],
            [
                'type'       => 'new_event_discussion',
                'module_id'  => 'event',
                'title'      => 'event::phrase.new_discussion_event_notification_type',
                'handler'    => NewEventDiscussion::class,
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 15,
            ],
            [
                'type'       => 'event_member',
                'module_id'  => 'event',
                'handler'    => EventRsvpNotification::class,
                'title'      => 'event::phrase.event_rsvp_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 16,
            ],
            [
                'type'       => 'event_approve_notification',
                'module_id'  => 'event',
                'handler'    => EventApproveNotifications::class,
                'title'      => 'event::phrase.event_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'sms', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 19,
            ],
        ];
    }

    public function getEvents(): array
    {
        return [
            'models.notify.created' => [
                ModelCreatedListener::class,
            ],
            'models.notify.deleted' => [
                ModelDeletedListener::class,
            ],
            'like.notification_to_callback_message' => [
                LikeNotificationMessageListener::class,
            ],
            'event.get_privacy_for_setting' => [
                PrivacyForSetting::class,
            ],
            'core.privacy.check_privacy_only_me' => [
                CheckPrivacyOnlyMeListener::class,
            ],
            'parseRoute' => [
                EventRouteListener::class,
            ],
            'comment.notification_to_callback_message' => [
                CommentNotificationMessageListener::class,
            ],
            'friend.invite.members' => [
                GetIdsUserInviteListener::class,
            ],
            'activity.feed.collection_icons' => [
                CollectIconListener::class,
            ],
            'activity.notify.approved_new_post_in_owner' => [
                ApprovedNewPostListener::class,
            ],
            'like.owner.can_like_item' => [
                CanLikeItemListener::class,
            ],
            'comment.owner.can_comment_item' => [
                CanCommentItemListener::class,
            ],
            'activity.get_privacy_detail_on_owner' => [
                GetPrivacyDetailOnOwnerListener::class,
            ],
            'user.extra_statistics' => [
                UserExtraStatisticListener::class,
            ],
            'core.collect_total_items_stat' => [
                CollectTotalItemsStatListener::class,
            ],
            'importer.completed' => [
                ImporterCompleted::class,
            ],
        ];
    }

    public function getUserValuePermissions(): array
    {
        return [
            Event::ENTITY_TYPE => [
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
                'how_long_time_must_wait_send_mass_email' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => 60,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 60,
                        UserRole::STAFF_USER  => 60,
                        UserRole::NORMAL_USER => 60,
                    ],
                ],
            ],
        ];
    }

    public function registerApplicationSchedule(Schedule $schedule): void
    {
        $schedule->job(resolve(UpdateStatusCodeInviteJob::class))->everyFiveMinutes();
        $schedule->job(resolve(CleanUpDeletedEventJob::class))->everySixHours();
    }

    public function getSiteStatContent(): ?array
    {
        return [
            Event::ENTITY_TYPE => 'ico-calendar-star-o',
        ];
    }

    public function getSavedTypes(): array
    {
        return [
            [
                'label' => __p('event::phrase.events'),
                'value' => 'event',
            ],
        ];
    }

    /**
     * @return array<string>
     */
    public function getSitemap(): array
    {
        return ['event', 'event_category'];
    }

    /**
     * @return array<int, mixed>
     */
    public function getAdMobPages(): array
    {
        return [
            [
                'path' => '/event',
                'name' => 'event::phrase.ad_mob_home_page',
            ],
            [
                'path' => '/event/:id',
                'name' => 'event::phrase.ad_mob_detail_page',
            ],
        ];
    }
}
