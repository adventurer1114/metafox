<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Activity\Listeners;

use Illuminate\Console\Scheduling\Schedule;
use MetaFox\Activity\Jobs\ExpiredSnoozeJob;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Post;
use MetaFox\Activity\Models\Share;
use MetaFox\Activity\Models\Snooze;
use MetaFox\Activity\Models\Type;
use MetaFox\Activity\Notifications\ApproveFeedNotification;
use MetaFox\Activity\Notifications\PendingFeedNotification;
use MetaFox\Activity\Notifications\ShareFeedNotification;
use MetaFox\Activity\Policies\FeedPolicy;
use MetaFox\Activity\Policies\Handlers\CanShare;
use MetaFox\Activity\Policies\PostPolicy;
use MetaFox\Activity\Policies\SharePolicy;
use MetaFox\Activity\Policies\SnoozePolicy;
use MetaFox\Activity\Policies\TypePolicy;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\UserRole;

/**
 * Class PackageSettingListener.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getPolicies(): array
    {
        return [
            Snooze::class => SnoozePolicy::class,
            Type::class   => TypePolicy::class,
            Feed::class   => FeedPolicy::class,
            Post::class   => PostPolicy::class,
            Share::class  => SharePolicy::class,
        ];
    }

    public function getPolicyHandlers(): array
    {
        return [
            Share::ENTITY_TYPE => CanShare::class,
        ];
    }

    public function getActivityForm(): array
    {
        return [
            Post::ENTITY_TYPE => [],
        ];
    }

    public function getActivityTypes(): array
    {
        return [
            [
                'type'                         => Post::ENTITY_TYPE,
                'entity_type'                  => Post::ENTITY_TYPE,
                'is_active'                    => true,
                'title'                        => 'activity::phrase.activity_post_type',
                'description'                  => 'user_posted_a_post_on_timeline',
                'is_system'                    => 0,
                'can_comment'                  => true,
                'can_like'                     => true,
                'can_share'                    => true,
                'can_edit'                     => true,
                'can_create_feed'              => true,
                'can_put_stream'               => true,
                'can_change_privacy_from_feed' => true,
            ],
            [
                'type'                         => Share::ENTITY_TYPE,
                'entity_type'                  => Share::ENTITY_TYPE,
                'is_active'                    => true,
                'title'                        => 'activity::phrase.activity_share_type',
                'description'                  => 'user_shared_a_post_to_newsfeed',
                'is_system'                    => 0,
                'can_comment'                  => true,
                'can_like'                     => true,
                'can_share'                    => true,
                'can_edit'                     => true,
                'can_create_feed'              => true,
                'can_put_stream'               => true,
                'can_change_privacy_from_feed' => true,
            ],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'activity_feed_pending',
                'module_id'  => 'activity',
                'title'      => 'activity::phrase.activity_feed_pending_notification_type',
                'handler'    => PendingFeedNotification::class,
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 2,
            ],
            [
                'type'       => 'activity_feed_approved',
                'module_id'  => 'activity',
                'title'      => 'activity::phrase.activity_feed_approved_notification_type',
                'handler'    => ApproveFeedNotification::class,
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 2,
            ],
            [
                'type'       => 'activity_share_notification',
                'module_id'  => 'activity',
                'title'      => 'activity::phrase.activity_share_notification_type',
                'handler'    => ShareFeedNotification::class,
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'mobilepush', 'webpush'],
                'ordering'   => 3,
            ],
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            '*' => [
                'share' => UserRole::LEVEL_REGISTERED,
            ],
            Feed::ENTITY_TYPE => [
                'view'             => UserRole::LEVEL_GUEST,
                'create'           => UserRole::LEVEL_PAGE,
                'update'           => UserRole::LEVEL_PAGE,
                'delete'           => UserRole::LEVEL_PAGE,
                'hide'             => UserRole::LEVEL_REGISTERED,
                'pin'              => UserRole::LEVEL_REGISTERED,
                'pin_home'         => UserRole::LEVEL_STAFF,
                'moderate'         => UserRole::LEVEL_STAFF,
                'report'           => UserRole::LEVEL_REGISTERED,
                'purchase_sponsor' => UserRole::LEVEL_REGISTERED,
                'sponsor'          => UserRole::LEVEL_REGISTERED,
            ],
            Snooze::ENTITY_TYPE => [
                'view'     => UserRole::LEVEL_REGISTERED,
                'create'   => UserRole::LEVEL_REGISTERED,
                'update'   => UserRole::LEVEL_REGISTERED,
                'delete'   => UserRole::LEVEL_REGISTERED,
                'moderate' => UserRole::LEVEL_STAFF,
            ],
            Share::ENTITY_TYPE => [
                'view'     => UserRole::LEVEL_GUEST,
                'create'   => UserRole::LEVEL_REGISTERED,
                'update'   => UserRole::LEVEL_REGISTERED,
                'delete'   => UserRole::LEVEL_REGISTERED,
                'moderate' => UserRole::LEVEL_STAFF,
                'approve'  => UserRole::LEVEL_STAFF,
            ],
            Post::ENTITY_TYPE => [
                'save'    => UserRole::LEVEL_REGISTERED,
                'report'  => UserRole::LEVEL_REGISTERED,
                'approve' => UserRole::LEVEL_STAFF,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'feed.allow_choose_sort_on_feeds' => ['value' => true],
            'feed.sort_default'               => ['value' => Browse::SORT_RECENT],
            'feed.enable_check_in'            => ['value' => true],
            'feed.enable_tag_friends'         => ['value' => true],
            'feed.enable_hide_feed'           => ['value' => true],
            'feed.limit_days'                 => ['value' => 0],
            'feed.only_friends'               => ['value' => false],
            'feed.refresh_time'               => ['value' => 60],
            'feed.top_stories_update'         => ['value' => 'comment'],
            'feed.total_likes_to_display'     => ['value' => 4],
            'feed.spam_check_status_updates'  => ['value' => 0],
            'feed.check_new_in_minutes'       => ['value' => 3],
            'feed.total_pin_in_homepage'      => ['value' => 3],
            'feed.total_pin_in_profile'       => ['value' => 3],
            'feed.add_comment_as_feed'        => ['value' => false],
            'feed.sponsored_feed_cache_time'  => ['value' => 60],
            'feed.schedule_on_feed'           => ['value' => false],
        ];
    }

    public function getEvents(): array
    {
        return [
            'models.notify.created' => [
                ModelCreatedListener::class,
            ],
            'models.notify.updated' => [
                ModelUpdatedListener::class,
            ],
            'models.notify.deleted' => [
                ModelDeletedListener::class,
            ],
            'models.notify.pending' => [
                ModelPendingListener::class,
            ],
            'models.notify.approved' => [
                ModelApprovedListener::class,
            ],
            'models.notify.published' => [
                ModelPublishedListener::class,
            ],
            'packages.installed' => [
                PackageInstalledListener::class,
            ],
            'activity.update_feed_item_privacy' => [
                UpdateFeedItemPrivacy::class,
            ],
            'activity.get_feed_id' => [
                GetFeedIdListener::class,
            ],
            'activity.get_feed' => [
                GetFeedListener::class,
            ],
            'activity.get_feed_by_item_id' => [
                GetFeedByItemIdListener::class,
            ],
            'activity.sponsor_in_feed' => [
                SponsorInFeedListener::class,
            ],
            'activity.push_feed_on_top' => [
                PushFeedOnTopListener::class,
            ],
            'activity.redundant' => [
                FeedRedundantListener::class,
            ],
            'activity.create_feed' => [
                CreateFeedListener::class,
            ],
            'activity.delete_feed' => [
                DeleteFeedListener::class,
            ],
            'core.total_view_updated' => [
                FeedRedundantListener::class,
            ],
            'activity.count_feed_pending_on_owner' => [
                CountFeedPendingOnOwnerListener::class,
            ],
            'packages.deleting' => [
                PackageDeletingListener::class,
            ],
            'activity.feed.deleted' => [
                FeedDeletedListener::class,
            ],
            'like.notification_to_callback_message' => [
                LikeNotificationMessageListener::class,
            ],
            'activity.feed_put_to_tag_stream' => [
                PutToStreamsListener::class,
            ],
            'activity.feed_delete_from_tag_stream' => [
                DeleteTagsStream::class,
            ],
            'comment.notification_to_callback_message' => [
                CommentNotificationMessageListener::class,
            ],
            'feed.composer' => [
                FeedComposerListener::class,
            ],
            'feed.composer.edit' => [
                FeedComposerEditListener::class,
            ],
            'activity.removed_feed' => [
                RemoveFeedListener::class,
            ],
            'activity.get_privacy_detail' => [
                GetPrivacyDetailListener::class,
            ],
            'activity.share.form' => [
                ShareFormListener::class,
            ],
            'activity.share.data_preparation' => [
                SharedDataPreparationListener::class,
            ],
            'feed.delete_item_by_user_and_owner' => [
                DeleteFeedByUserAndOwnerListener::class,
            ],
            'activity.feed.count' => [
                CountOwnerFeedListener::class,
            ],
            'user.deleted' => [
                UserDeletedListener::class,
            ],
            'activity.feed.mark_as_pending' => [
                MarkAsPendingListener::class,
            ],
            'activity.feed.create_from_resource' => [
                CreateFeedFromResourceListener::class,
            ],
            'user.registration.extra_field.create' => [
                UserRegistrationListener::class,
            ],
            'activity.check_spam_status' => [
                [ActivityCheckSpamStatusListener::class, 'checkSpamStatus'],
            ],
            'activity.has_feature' => [
                [HasActivityFeature::class, 'hasFeature'],
            ],
            'importer.completed' => [
                ImporterCompleted::class,
            ],
            'activity.feed.mark_as_approved' => [
                MarkAsApproveListener::class,
            ],
        ];
    }

    public function registerApplicationSchedule(Schedule $schedule): void
    {
        $schedule->job(ExpiredSnoozeJob::class)->everyFiveMinutes()->withoutOverlapping();
    }

    public function getUserPrivacy(): array
    {
        return [
            'feed.view_wall' => [
                'phrase' => 'activity::phrase.user_privacy.who_can_view_your_activities_section_on_your_profile_page',
            ],
            'feed.share_on_wall' => [
                'phrase' => 'activity::phrase.user_privacy.who_can_post_on_your_profile',
            ],
        ];
    }

    public function getUserPrivacyResource(): array
    {
        return [
            'user' => [
                'feed.view_wall' => [
                    'list' => [
                        MetaFoxPrivacy::EVERYONE,
                        MetaFoxPrivacy::MEMBERS,
                        MetaFoxPrivacy::FRIENDS,
                        MetaFoxPrivacy::ONLY_ME,
                    ],
                ],
                'feed.share_on_wall' => [
                    'default' => MetaFoxPrivacy::FRIENDS,
                    'list'    => [
                        MetaFoxPrivacy::MEMBERS,
                        MetaFoxPrivacy::FRIENDS,
                        MetaFoxPrivacy::ONLY_ME,
                    ],
                ],
            ],
            'group' => [
                'feed.share_on_wall' => [
                    'phrase' => 'activity::phrase.user_privacy.who_can_share_a_post',
                ],
            ],
            'page' => [
                'feed.share_on_wall' => [
                    'phrase' => 'activity::phrase.user_privacy.who_can_share_a_post',
                ],
            ],
        ];
    }

    public function getDefaultPrivacy(): array
    {
        return [
            Feed::ENTITY_TYPE => [
                'phrase'  => 'activity::phrase.feed_default_privacy',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
            Share::ENTITY_TYPE => [
                'phrase'  => 'activity::phrase.share_default_privacy',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getItemTypes(): array
    {
        return [
            Feed::ENTITY_TYPE,
            Post::ENTITY_TYPE,
            Share::ENTITY_TYPE,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getActivityPointSettings(): array
    {
        return [
            'metafox/activity' => [
                [
                    'name'               => Post::ENTITY_TYPE . '.post_on_wall',
                    'action'             => 'post_on_wall',
                    'module_id'          => 'activity',
                    'package_id'         => 'metafox/activity',
                    'description_phrase' => 'activity::activitypoint.setting_post_on_wall_description',
                ],
                [
                    'name'               => Post::ENTITY_TYPE . '.post_on_other',
                    'action'             => 'post_on_other',
                    'module_id'          => 'activity',
                    'package_id'         => 'metafox/activity',
                    'description_phrase' => 'activity::activitypoint.setting_post_on_other_description',
                ],
            ],
        ];
    }

    public function getSavedTypes(): array
    {
        return [
            [
                'label' => __p('activity::phrase.user_status'),
                'value' => 'activity_post',
            ],
        ];
    }

    /**
     * @return array<string>
     */
    public function getSitemap(): array
    {
        return ['feed'];
    }

    /**
     * @return array<int, mixed>
     */
    public function getAdMobPages(): array
    {
        return [
            [
                'path' => '/feed/:id',
                'name' => 'activity::phrase.ad_mob_feed_detail_page',
            ],
        ];
    }
}
