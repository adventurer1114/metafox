<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Comment\Listeners;

use MetaFox\Comment\Models\Comment;
use MetaFox\Comment\Notifications\CommentNotification;
use MetaFox\Comment\Policies\CommentPolicy;
use MetaFox\Comment\Policies\Handlers\CanComment;
use MetaFox\Comment\Support\Helper;
use MetaFox\Platform\MetaFoxDataType;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getEvents(): array
    {
        return [
            'models.notify.created' => [
                ModelCreatedListener::class,
            ],
            'models.notify.deleting' => [
                ModelDeletingListener::class,
            ],
            'comment.related_comments' => [
                RelatedCommentsListener::class,
            ],
            'comment.related_comments.item_detail' => [
                RelatedCommentsItemDetailListener::class,
            ],
            'like.notification_to_callback_message' => [
                LikeNotificationMessageListener::class,
            ],
            'comment.delete_by_item' => [
                DeleteCommentByItemListener::class,
            ],
            'comment.get_user_by_item' => [
                UserCommentByItemListener::class,
            ],
            'comment.relevant_comment_by_id' => [
                RelevantCommentByIdListener::class,
            ],
            'comment.related_comments.total_hidden' => [
                TotalHiddenListener::class,
            ],
            'like.owner.notification' => [
                LikeNotificationListener::class,
            ],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'new_comment',
                'module_id'  => 'comment',
                'title'      => 'comment::phrase.new_comment_notification_type',
                'handler'    => CommentNotification::class,
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 3,
            ],
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            '*' => [
                'comment' => UserRole::LEVEL_REGISTERED,
            ],
            Comment::ENTITY_TYPE => [
                'comment'            => UserRole::LEVEL_REGISTERED,
                'update'             => UserRole::LEVEL_REGISTERED,
                'delete'             => UserRole::LEVEL_REGISTERED,
                'hide'               => UserRole::LEVEL_REGISTERED,
                'auto_approved'      => UserRole::LEVEL_REGISTERED,
                'moderate'           => UserRole::LEVEL_STAFF,
                'like'               => UserRole::LEVEL_REGISTERED,
                'delete_on_own_item' => UserRole::LEVEL_REGISTERED,
                'update_on_own_item' => UserRole::LEVEL_REGISTERED,
                'report'             => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'prefetch_comments_on_feed'          => ['value' => 1],
            'enable_photo'                       => ['value' => true],
            'enable_sticker'                     => ['value' => true],
            'enable_emoticon'                    => ['value' => true],
            'enable_thread'                      => ['value' => true],
            'show_reply'                         => ['value' => true],
            'prefetch_replies_on_feed'           => ['value' => 1],
            'enable_hash_check'                  => ['value' => true],
            'comments_to_check'                  => ['value' => 10],
            'total_minutes_to_wait_for_comments' => ['value' => 1],
            'sort_by'                            => ['value' => Helper::SORT_ALL],
            'prefetch_comments_on_item_detail'   => ['value' => 4],
            'prefetch_replies_on_item_detail'    => ['value' => 2],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getPolicies(): array
    {
        return [
            Comment::class => CommentPolicy::class,
        ];
    }

    public function getPolicyHandlers(): array
    {
        return [
            Comment::ENTITY_TYPE => CanComment::class,
        ];
    }

    public function getUserValuePermissions(): array
    {
        return [
            Comment::ENTITY_TYPE => [
                'flood_control' => [
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

    public function getCaptchaRules(): array
    {
        return ['create_comment'];
    }

    public function getUserPrivacy(): array
    {
        return [
            'comment.view_browse_comments' => [
                'phrase' => 'comment::phrase.user_privacy.who_can_view_browse_comments',
            ],
        ];
    }

    public function getUserPrivacyResource(): array
    {
        return [
            'page' => [
                'comment.view_browse_comments',
            ],
        ];
    }
}
