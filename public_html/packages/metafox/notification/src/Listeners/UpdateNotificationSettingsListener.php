<?php

namespace MetaFox\Notification\Listeners;

use MetaFox\Notification\Repositories\TypeRepositoryInterface;
use MetaFox\Platform\Contracts\User;

/**
 * Class UpdateNotificationSettingsListener.
 * @ignore
 * @codeCoverageIgnore
 */
class UpdateNotificationSettingsListener
{
    /**
     * @param User|null         $user
     * @param array<string,int> $attributes
     *
     * @return bool
     */
    public function handle(?User $user, array $attributes): bool
    {
        return resolve(TypeRepositoryInterface::class)->updateNotificationSettingsByChannel($user, $attributes);
    }
}
