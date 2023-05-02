<?php

namespace MetaFox\Activity\Policies\Traits;

use MetaFox\Activity\Contracts\ActivitySnoozeManager;
use MetaFox\Platform\Contracts\User as User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;

trait HideAllPolicyTrait
{
    private function prepareCheck(User $user, ?User $owner = null, ?bool $isProfileFeed = null): bool
    {
        // In profile feed context, don't allow hide all / snooze.
        if ($isProfileFeed === true) {
            return false;
        }

        // Case user was deleted.
        if ($owner === null) {
            return false;
        }

        /*
         * TODO: remove it when implementing snooze user feature
         */
        return false;

        if (!$user->hasPermissionTo('activity_snooze.create')) {
            return false;
        }

        if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
            return false;
        }

        if ($user->entityId() == $owner->entityId()) {
            return false;
        }

        return true;
    }

    public function snooze(User $user, ?User $owner = null, ?bool $isProfileFeed = null): bool
    {
        if ($this->prepareCheck($user, $owner, $isProfileFeed) === false) {
            return false;
        }

        $service = resolve(ActivitySnoozeManager::class);

        $isSnoozed = $service->isSnooze($user, $owner);

        if ($isSnoozed) {
            return false;
        }

        return true;
    }

    public function hideAll(User $user, ?User $owner = null, ?bool $isProfileFeed = null): bool
    {
        if ($this->prepareCheck($user, $owner, $isProfileFeed) === false) {
            return false;
        }

        $service = resolve(ActivitySnoozeManager::class);

        $isHideAll = $service->isHideAll($user, $owner);

        if ($isHideAll) {
            return false;
        }

        return true;
    }
}
