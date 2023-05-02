<?php

namespace MetaFox\Friend\Listeners;

use MetaFox\Friend\Support\Facades\Friend;
use MetaFox\Platform\Contracts\User;

/**
 * Class GetFriendShipListener.
 * @ignore
 * @codeCoverageIgnore
 */
class GetFriendShipListener
{
    /**
     * @param User|null $context
     * @param User      $user
     *
     * @return int
     */
    public function handle(?User $context, User $user): int
    {
        if (!$context) {
            return 4;
        }

        return Friend::getFriendship($context, $user);
    }
}
