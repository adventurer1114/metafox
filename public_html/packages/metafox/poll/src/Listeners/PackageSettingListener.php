<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Poll\Listeners;

use MetaFox\Platform\MetaFoxDataType;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;
use MetaFox\Poll\Listeners\AllowedTypeListener;
use MetaFox\Poll\Models\Poll;
use MetaFox\Poll\Notifications\PendingPollNotification;
use MetaFox\Poll\Notifications\PollApproveNotification;
use MetaFox\Poll\Notifications\PollResultNotification;
use MetaFox\Poll\Policies\PollPolicy;
use MetaFox\Poll\Support\Handlers\EditPermissionListener;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getActivityTypes(): array
    {
        return [
            [
                'type'                         => Poll::ENTITY_TYPE,
                'entity_type'                  => Poll::ENTITY_TYPE,
                'is_active'                    => true,
                'title'                        => 'poll::phrase.poll_type',
                'description'                  => 'added_a_poll',
                'is_system'                    => 0,
                'can_comment'                  => true,
                'can_like'                     => true,
                'can_share'                    => true,
                'can_edit'                     => true,
                'can_create_feed'              => true,
                'can_put_stream'               => true,
                'can_change_privacy_from_feed' => true,
                'can_redirect_to_detail'       => true,
            ],
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            Poll::ENTITY_TYPE => [
                'view'   => UserRole::LEVEL_GUEST,
                'create' => UserRole::LEVEL_PAGE,
                'update' => UserRole::LEVEL_PAGE,
                // 'publish'                 => UserRole::LEVEL_REGISTERED,
                'delete'   => UserRole::LEVEL_PAGE,
                'moderate' => UserRole::LEVEL_STAFF,
                'feature'  => UserRole::LEVEL_REGISTERED,
                'like'     => UserRole::LEVEL_REGISTERED,
                'share'    => UserRole::LEVEL_REGISTERED,
                'comment'  => UserRole::LEVEL_REGISTERED,
                'report'   => UserRole::LEVEL_REGISTERED,
                'save'     => UserRole::LEVEL_REGISTERED,
                'approve'  => UserRole::LEVEL_STAFF,
                // 'purchase_sponsor'        => UserRole::LEVEL_REGISTERED,
                // 'sponsor'                 => UserRole::LEVEL_REGISTERED,
                // 'sponsor_in_feed'         => UserRole::LEVEL_REGISTERED,
                'auto_approved'           => UserRole::LEVEL_PAGE,
                'view_result_before_vote' => UserRole::LEVEL_REGISTERED,
                'view_result_after_vote'  => UserRole::LEVEL_REGISTERED,
                'change_own_vote'         => UserRole::LEVEL_REGISTERED,
                'upload_image'            => UserRole::LEVEL_REGISTERED,
                'vote_own'                => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    public function getUserPrivacy(): array
    {
        return [
            'poll.share_polls' => [
                'phrase' => 'poll::phrase.user_privacy.who_can_share_polls',
            ],
            'poll.view_browse_polls' => [
                'phrase' => 'poll::phrase.user_privacy.who_can_view_browse_polls',
            ],
        ];
    }

    public function getUserPrivacyResource(): array
    {
        return [
            'page' => [
                'poll.share_polls',
                'poll.view_browse_polls',
            ],
            'group' => [
                'poll.share_polls',
            ],
        ];
    }

    public function getDefaultPrivacy(): array
    {
        return [
            Poll::ENTITY_TYPE => [
                'phrase'  => 'poll::phrase.polls',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getProfileMenu(): array
    {
        return [
            Poll::ENTITY_TYPE => [
                'phrase'  => 'poll::phrase.polls',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'is_image_required'   => ['value' => false],
            'minimum_name_length' => ['value' => 3],
            'maximum_name_length' => ['value' => 100],
        ];
    }

    public function getActivityForm(): array
    {
        return [
            Poll::ENTITY_TYPE => [
                // setting more here.
            ],
        ];
    }

    public function getEvents(): array
    {
        return [
            'feed.composer.prepare_data' => [
                PrepareFeedDataListener::class,
            ],
            'feed.composer' => [
                FeedComposerListener::class,
            ],
            'feed.composer.edit' => [
                FeedComposerEditListener::class,
            ],
            'like.notification_to_callback_message' => [
                LikeNotificationMessageListener::class,
            ],
            'forum.thread.integrated_item.initialize' => [
                ThreadIntegrationListener::class,
                MobileThreadIntergationListener::class,
            ],
            'forum.thread.integrated_item.create' => [
                CreateThreadIntegrationListener::class,
            ],
            'forum.thread.integrated_item.edit_initialize' => [
                ThreadIntegrationEditListener::class,
            ],
            'forum.thread.integrated_item.update' => [
                UpdateThreadIntegrationListener::class,
            ],
            'forum.thread.integrated_item.delete' => [
                DeleteThreadIntegrationListener::class,
            ],
            'forum.thread.integrated_item.copy' => [
                ThreadIntegrationCopyListener::class,
            ],
            'activity.update_feed_item_privacy' => [
                UpdateFeedItemPrivacyListener::class,
            ],
            'comment.notification_to_callback_message' => [
                CommentNotificationMessageListener::class,
            ],
            'core.collect_total_items_stat' => [
                CollectTotalItemsStatListener::class,
            ],
            'user.deleted' => [
                UserDeletedListener::class,
            ],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'poll_notification',
                'module_id'  => 'poll',
                'handler'    => PollResultNotification::class,
                'title'      => 'poll::phrase.poll_result_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'sms', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 17,
            ],
            [
                'type'       => 'poll_approve_notification',
                'module_id'  => 'poll',
                'handler'    => PollApproveNotification::class,
                'title'      => 'poll::phrase.poll_approve_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'sms', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 18,
            ],
        ];
    }

    public function getPolicies(): array
    {
        return [
            Poll::class => PollPolicy::class,
        ];
    }

    public function getUserValuePermissions(): array
    {
        return [
            Poll::ENTITY_TYPE => [
                'maximum_answers_count' => [
                    'description' => 'how_many_answers_can_members_add_to_their_polls',
                    'type'        => MetaFoxDataType::INTEGER,
                    'default'     => 6,
                    'roles'       => [
                        UserRole::ADMIN_USER  => 6,
                        UserRole::STAFF_USER  => 6,
                        UserRole::NORMAL_USER => 6,
                    ],
                    'extra' => [
                        'fieldCreator' => [EditPermissionListener::class, 'maximumAnswersCount'],
                    ],
                ],
                'flood_control' => [
                    'description' => 'specify_how_many_minutes_should_a_user_wait_before_they_can_submit_another_poll',
                    'type'        => MetaFoxDataType::INTEGER,
                    'default'     => 0,
                    'roles'       => [
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
            ],
        ];
    }

    public function getSiteStatContent(): ?array
    {
        return [
            Poll::ENTITY_TYPE => 'ico-bar-chart2',
        ];
    }

    public function getSavedTypes(): array
    {
        return [
            [
                'label' => __p('poll::phrase.polls'),
                'value' => 'poll',
            ],
        ];
    }

    /**
     * @return array<string>
     */
    public function getSitemap(): array
    {
        return ['poll'];
    }
}
