<?php

namespace MetaFox\Activity\Policies\Contracts;

use MetaFox\Platform\Contracts\User as User;

interface HideAllPolicyInterface
{
    public function snooze(User $user, ?User $owner = null, bool $isProfileFeed = false): bool;

    public function hideAll(User $user, ?User $owner = null, bool $isProfileFeed = false): bool;
}
