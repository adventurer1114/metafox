<?php

namespace MetaFox\Friend\Listeners;

use MetaFox\Friend\Repositories\TagFriendRepositoryInterface;
use MetaFox\Platform\Contracts\HasTaggedFriend;
use MetaFox\Platform\Contracts\User;

/**
 * Class CreateTagFriendsListener.
 * @ignore
 * @codeCoverageIgnore
 */
class CreateTagFriendsListener
{
    /**
     * @param User|null       $context
     * @param HasTaggedFriend $item
     * @param int[]           $tagFriends
     * @param string|null     $typeId
     *
     * @return bool
     */
    public function handle(?User $context, HasTaggedFriend $item, array $tagFriends, ?string $typeId = null): bool
    {
        if (!$context) {
            return false;
        }

        return resolve(TagFriendRepositoryInterface::class)->createTagFriend($context, $item, $tagFriends, $typeId);
    }
}
