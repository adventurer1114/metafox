<?php

namespace MetaFox\Friend\Listeners;

use MetaFox\Friend\Repositories\FriendRepositoryInterface;

/**
 * Class CountTotalFriendListener.
 * @ignore
 * @codeCoverageIgnore
 */
class CountTotalFriendListener
{
    /**
     * @param int $userId
     *
     * @return int
     */
    public function handle(int $userId): int
    {
        return resolve(FriendRepositoryInterface::class)->countTotalFriends($userId);
    }
}
