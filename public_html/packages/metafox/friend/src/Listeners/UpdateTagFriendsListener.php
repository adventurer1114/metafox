<?php

namespace MetaFox\Friend\Listeners;

use MetaFox\Friend\Repositories\TagFriendRepositoryInterface;
use MetaFox\Platform\Contracts\HasTaggedFriend;
use MetaFox\Platform\Contracts\User;

/**
 * Class UpdateTagFriendsListener.
 * @ignore
 * @codeCoverageIgnore
 */
class UpdateTagFriendsListener
{
    /**
     * @param  User|null       $context
     * @param  HasTaggedFriend $item
     * @param  int[]           $tagFriends
     * @param  string|null     $typeId
     * @return bool
     */
    public function handle(?User $context, HasTaggedFriend $item, array $tagFriends, ?string $typeId = null): bool
    {
        if (!$context) {
            return true;
        }
        resolve(TagFriendRepositoryInterface::class)->updateTagFriend($context, $item, $tagFriends, $typeId);

        return true;
    }
}
