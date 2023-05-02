<?php

namespace MetaFox\Friend\Listeners;

use MetaFox\Friend\Models\Friend;
use MetaFox\Friend\Models\FriendList;
use MetaFox\Friend\Models\FriendRequest;
use MetaFox\Friend\Notifications\FriendAccepted;
use MetaFox\Friend\Notifications\FriendRequested;
use MetaFox\Friend\Notifications\FriendTag;
use MetaFox\Friend\Policies\FriendListPolicy;
use MetaFox\Friend\Policies\FriendPolicy;
use MetaFox\Friend\Policies\FriendRequestPolicy;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;

/**
 * Class PackageSettingListener.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getEvents(): array
    {
        return [
            'friendList.check_privacy_list' => [
                CheckPrivacyListListener::class,
            ],
            'friend.get_friend_ship' => [
                GetFriendShipListener::class,
            ],
            'friend.is_friend' => [
                IsFriendListener::class,
            ],
            'friend.simple_friends' => [
                [IsFriendListener::class, 'getSimpleFriends'],
            ],
            'friend.can_add_friend' => [
                CanAddFriendListener::class,
            ],
            'friend.create_tag_friends' => [
                CreateTagFriendsListener::class,
            ],
            'core.badge_counter' => [
                CountNewFriendRequestListener::class,
            ],
            'friend.update_tag_friends' => [
                UpdateTagFriendsListener::class,
            ],
            'friend.get_tag_friends' => [
                GetTagFriendsListener::class,
            ],
            'friend.get_tag_friend' => [
                GetTagFriendListener::class,
            ],
            'friend.get_owner_tag_friends' => [
                GetPhotoTagFriendsListener::class,
            ],
            'friend.get_tag_friend_by_id' => [
                GetTagFriendByIdListener::class,
            ],
            'friend.delete_tag_friend' => [
                DeleteTagFriendListener::class,
            ],
            'friend.delete_item_tag_friend' => [
                DeleteItemTagFriendListener::class,
            ],
            'friend.friend_ids' => [
                GetFriendIdsListener::class,
            ],
            'friend.is_friend_of_friend' => [
                IsFriendOfFriendListener::class,
            ],
            'friend.count_total_friend' => [
                CountTotalFriendListener::class,
            ],
            'friend.count_total_mutual_friend' => [
                CountTotalMutualFriendListener::class,
            ],
            'friend.count_total_friend_request' => [
                CountTotalFriendRequestListener::class,
            ],
            'core.parse_content' => [
                ParseFeedContentListener::class,
            ],
            'core.strip_content' => [
                StripFeedContentListener::class,
            ],
            'friend.get_suggestion' => [
                GetSuggestionListener::class,
            ],
            'user.blocked' => [
                UserBlockedListener::class,
            ],
            'models.notify.deleting' => [
                ModelDeletingListener::class,
            ],
            'user.permissions.extra' => [
                UserExtraPermissionListener::class,
            ],
            'friend.mention.builder' => [
                FriendMentionBuilderListener::class,
            ],
            'activity.share.data_preparation' => [
                SharedDataPreparationListener::class,
            ],
            'activity.share.rules' => [
                ShareRuleListener::class,
            ],
            'activity.share.form' => [
                ShareFormListener::class,
            ],
            'user.registration.extra_field.create' => [
                UserRegistrationExtraFieldsCreateListener::class,
            ],
            'user.deleted' => [
                UserDeletedListener::class,
            ],
            'friend.list.get' => [
                GetFriendListListener::class,
            ],
            'friend.friend_list.create' => [
                CreateFriendListListener::class,
            ],
            'friend.get_eloquent_builder' => [
                GetEloquentBuilderListener::class,
            ],
        ];
    }

    public function getActivityTypes(): array
    {
        return [
            [
                'type'            => Friend::ENTITY_TYPE,
                'entity_type'     => Friend::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'friend::phrase.friend_type',
                'description'     => 'is_now_friend_with',
                'is_system'       => 0,
                'can_comment'     => false,
                'can_like'        => false,
                'can_share'       => false,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => true,
                'action_on_feed'  => true,
            ],
        ];
    }

    public function getUserPrivacy(): array
    {
        return [
            'friend.view_friend' => [
                'phrase' => 'friend::phrase.user_privacy.who_can_view_your_friends_list',
            ],
            'friend.send_request' => [
                'phrase' => 'friend::phrase.user_privacy.who_can_send_you_a_friend_request',
            ],
        ];
    }

    public function getUserPrivacyResource(): array
    {
        return [
            'user' => [
                'friend.view_friend' => [
                    'default' => MetaFoxPrivacy::EVERYONE,
                    'list'    => [
                        MetaFoxPrivacy::EVERYONE,
                        MetaFoxPrivacy::MEMBERS,
                        MetaFoxPrivacy::FRIENDS,
                        MetaFoxPrivacy::ONLY_ME,
                    ],
                ],
                'friend.send_request' => [
                    'default' => MetaFoxPrivacy::MEMBERS,
                    'list'    => [
                        MetaFoxPrivacy::MEMBERS,
                        MetaFoxPrivacy::FRIENDS_OF_FRIENDS,
                        MetaFoxPrivacy::ONLY_ME,
                    ],
                ],
            ],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'friend_accepted',
                'module_id'  => 'friend',
                'title'      => 'friend::phrase.friend_accept_notification_type',
                'handler'    => FriendAccepted::class,
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 10,
            ],
            [
                'type'       => 'friend_requested',
                'module_id'  => 'friend',
                'title'      => 'friend::phrase.friend_request_notification_type',
                'handler'    => FriendRequested::class,
                'is_request' => 1,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail'],
                'ordering'   => 11,
            ],
            [
                'type'       => 'friend_tag',
                'module_id'  => 'friend',
                'handler'    => FriendTag::class,
                'title'      => 'friend::phrase.friend_tag_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 12,
            ],
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            FriendRequest::ENTITY_TYPE => [
                'create' => UserRole::LEVEL_REGISTERED,
            ],
            FriendList::ENTITY_TYPE => [
                'view'   => UserRole::LEVEL_REGISTERED,
                'create' => UserRole::LEVEL_REGISTERED,
                'update' => UserRole::LEVEL_REGISTERED,
                'delete' => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'friend_request_total' => ['value' => 10],
            //            'enable_birthday_notices'              => ['value' => true],
            //            'days_to_check_for_birthday'           => ['value' => 7],
            'friend_suggestion_friend_check_count' => ['value' => 50],
            'enable_friend_suggestion'             => ['value' => true],
            'friend_suggestion_timeout'            => ['value' => 1440],
            'friend_suggestion_user_based'         => ['value' => false],
            'friend_cache_limit'                   => ['value' => 100],
            'friends_only_profile'                 => ['value' => false],
            'cache_rand_list_of_friends'           => ['value' => 60],
            'friendship_direction'                 => ['value' => MetaFoxConstant::TWO_WAY_FRIENDSHIPS], // @Todo this feature is pending
            'maximum_name_length'                  => ['value' => 64],
        ];
    }

    public function getPolicies(): array
    {
        return [
            Friend::class        => FriendPolicy::class,
            FriendRequest::class => FriendRequestPolicy::class,
            FriendList::class    => FriendListPolicy::class,
        ];
    }

    public function getProfileMenu(): array
    {
        return [
            Friend::ENTITY_TYPE => [
                'phrase'      => 'friend::phrase.friends',
                'default'     => MetaFoxPrivacy::EVERYONE,
                'is_editable' => false,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getActivityPointSettings(): array
    {
        return [
            'metafox/friend' => [
                [
                    'name'               => Friend::ENTITY_TYPE . '.added_new_friend',
                    'action'             => 'added_new_friend',
                    'module_id'          => 'friend',
                    'package_id'         => 'metafox/friend',
                    'description_phrase' => 'friend::activitypoint.setting_added_new_friend_description',
                ],
            ],
        ];
    }

    /**
     * @return array<int, mixed>
     */
    public function getAdMobPages(): array
    {
        return [
            [
                'path' => '/friend',
                'name' => 'friend::phrase.ad_mob_friend_home_page',
            ],
            [
                'path' => '/friend/friend_list/:id',
                'name' => 'friend::phrase.ad_mob_friend_list_detail_page',
            ],
        ];
    }
}
