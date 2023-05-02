<?php

namespace MetaFox\Activity\Listeners;

use MetaFox\Activity\Support\Facades\ActivitySubscription;
use MetaFox\Activity\Support\Support;
use MetaFox\Platform\Contracts\User;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;

class UserRegistrationListener
{
    public function handle(?User $user, array $attributes): void
    {
        if (!$user) {
            return;
        }

        $superAdmin = resolve(UserRepositoryInterface::class)->getSuperAdmin();

        if (null === $superAdmin) {
            return;
        }

        ActivitySubscription::addSubscription(
            $user->entityId(),
            $superAdmin->entityId(),
            true,
            Support::ACTIVITY_SUBSCRIPTION_VIEW_SUPER_ADMIN_FEED
        );
    }
}
