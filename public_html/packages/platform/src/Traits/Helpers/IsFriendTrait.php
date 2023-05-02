<?php

namespace MetaFox\Platform\Traits\Helpers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasTaggedFriend;
use MetaFox\Platform\Contracts\User;

/**
 * Trait IsFriendTrait.
 */
trait IsFriendTrait
{
    public function isFriend(User $context, ?User $user = null): bool
    {
        return app_active('metafox/friend')
            && $user instanceof User
            && app('events')->dispatch('friend.is_friend', [$context->id, $user->id], true);
    }

    public function canAddFriend(User $context, ?User $user = null): bool
    {
        if (!app_active('metafox/friend')) {
            return false;
        }

        if (!$user instanceof User) {
            return false;
        }

        if (!app('events')->dispatch('friend.can_add_friend', [$context, $user], true)) {
            return false;
        }

        return true;
    }

    public function getTaggedFriends(?Entity $item, int $limit = 10): ?Builder
    {
        if ($item === null) {
            return null;
        }

        if (!app_active('metafox/friend')) {
            return null;
        }

        if (!$item instanceof HasTaggedFriend) {
            return null;
        }

        /** @var Builder|null $tagFriends */
        $tagFriends = app('events')->dispatch('friend.get_tag_friends', [$item, $limit], true);

        if (!$tagFriends instanceof Builder) {
            return null;
        }

        return $tagFriends;
    }

    public function getTaggedFriend(?Entity $item, User $friend)
    {
        if ($item === null) {
            return null;
        }

        if (!app_active('metafox/friend')) {
            return null;
        }

        if (!$item instanceof HasTaggedFriend) {
            return null;
        }

        return app('events')->dispatch('friend.get_tag_friend', [$item, $friend], true);
    }

    public function countTotalFriend(int $userId): int
    {
        if (!app_active('metafox/friend')) {
            return 0;
        }

        /** @var int $totalFriend */
        $totalFriend = app('events')->dispatch('friend.count_total_friend', [$userId], true);

        return !empty($totalFriend) ? $totalFriend : 0;
    }

    public function countTotalMutualFriend(int $contextId, int $userId): int
    {
        if (!app_active('metafox/friend')) {
            return 0;
        }

        /** @var int $totalMutualFriend */
        $totalMutualFriend = app('events')->dispatch('friend.count_total_mutual_friend', [$contextId, $userId], true);

        return !empty($totalMutualFriend) ? $totalMutualFriend : 0;
    }

    public function countTotalFriendRequest(User $user): int
    {
        if (!app_active('metafox/friend')) {
            return 0;
        }

        /** @var int $totalFriendRequest */
        $totalFriendRequest = app('events')->dispatch('friend.count_total_friend_request', [$user], true);

        return !empty($totalFriendRequest) ? $totalFriendRequest : 0;
    }
}
