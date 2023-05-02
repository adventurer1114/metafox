<?php

namespace MetaFox\Friend\Listeners;

use MetaFox\Friend\Repositories\FriendRequestRepositoryInterface;
use MetaFox\User\Models\User;

/**
 * Class CountTotalFriendRequestListener.
 * @ignore
 * @codeCoverageIgnore
 */
class CountTotalFriendRequestListener
{
    /**
     * @param User $user
     *
     * @return int
     */
    public function handle(User $user): int
    {
        return resolve(FriendRequestRepositoryInterface::class)->countTotalFriendRequest($user);
    }
}
