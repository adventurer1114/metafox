<?php

namespace MetaFox\Event\Listeners;

use MetaFox\Event\Models\Event;
use MetaFox\Platform\Contracts\User;

/**
 * Class CheckPrivacyOnlyMeListener.
 * @ignore
 */
class CheckPrivacyOnlyMeListener
{
    /**
     * @return ?bool
     */
    public function handle(User $user, User $owner): ?bool
    {
        if ($owner instanceof Event) {
            if ($owner->isAdmin($user)) {
                return true;
            }
        }

        return null;
    }
}
