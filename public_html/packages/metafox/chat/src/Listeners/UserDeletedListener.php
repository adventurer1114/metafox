<?php

namespace MetaFox\Chat\Listeners;

use MetaFox\Chat\Repositories\SubscriptionRepositoryInterface;
use MetaFox\Platform\Contracts\User;

class UserDeletedListener
{
    public function handle(User $user): void
    {
        resolve(SubscriptionRepositoryInterface::class)->deleteUserSubscriptions($user->entityId());
    }
}
