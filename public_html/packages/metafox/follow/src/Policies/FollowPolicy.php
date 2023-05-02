<?php

namespace MetaFox\Follow\Policies;

use MetaFox\Platform\Contracts\HasPolicy;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class FollowPolicy.
 * @ignore
 * @codeCoverageIgnore
 */
class FollowPolicy implements HasPolicy
{
    use HasPolicyTrait;

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @param User $owner
     *
     * @return bool
     */
    public function addFollow(User $user, User $owner): bool
    {
        if ($user->isGuest()) {
            return false;
        }

        // Check can view on owner.
        if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
            return false;
        }

        if (!$owner instanceof HasPrivacyMember) {
            if (!UserPrivacy::hasAccess($user, $owner, 'follow.add_follow')) {
                return false;
            }
        }

        if ($owner->entityId() == $user->entityId()) {
            return false;
        }

        return true;
    }

    public function viewOnProfilePage(User $user, User $owner): bool
    {
        if (!UserPrivacy::hasAccess($user, $owner, 'profile.view_profile')) {
            return false;
        }

        if (!UserPrivacy::hasAccess($user, $owner, 'follow.view_following')) {
            return false;
        }

        return true;
    }
}
