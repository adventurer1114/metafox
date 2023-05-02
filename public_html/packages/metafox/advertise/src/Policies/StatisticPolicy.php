<?php

namespace MetaFox\Advertise\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;

/**
 * stub: /packages/policies/model_policy.stub.
 */

/**
 * Class StatisticPolicy.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class StatisticPolicy implements ResourcePolicyInterface
{
    use HandlesAuthorization;

    protected string $type = 'advertise_statistic';

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('advertise_statistic.view')) {
            return false;
        }

        if ($owner instanceof User) {
            if (!$this->viewOwner($user, $owner)) {
                return false;
            }
        }

        return true;
    }

    public function viewOwner(User $user, ?User $owner = null): bool
    {
        if ($owner == null) {
            return false;
        }
        // Check can view on owner.
        if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
            return false;
        }

        //if (UserPrivacy::hasAccess($user, $owner, 'advertise_statistic.view_browse_blogs') == false) {
        //    return false;
        //}

        return true;
    }

    public function view(User $user, Content $resource): bool
    {
        if (!$user->hasPermissionTo('advertise_statistic.view')) {
            return false;
        }

        $owner = $resource->owner;

        if ($this->viewOwner($user, $owner) == false) {
            return false;
        }

        // Check can view on resource.
        if (PrivacyPolicy::checkPermission($user, $resource) == false) {
            return false;
        }

        // Check setting view on resource.

        return true;
    }

    public function create(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('advertise_statistic.create')) {
            return false;
        }

        if ($owner instanceof User) {
            if ($owner->entityId() != $user->entityId()) {
                if ($owner->entityType() == 'user') {
                    return false;
                }

                // Check can view on owner.
                if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
                    return false;
                }

                //if (!UserPrivacy::hasAccess($user, $owner, 'advertise_statistic.share_blogs')) {
                //    return false;
                //}
            }
        }

        return true;
    }

    public function update(User $user, ?Content $resource = null): bool
    {
        if ($user->hasPermissionTo('advertise_statistic.moderate')) {
            return true;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return $user->hasPermissionTo('advertise_statistic.update');
    }

    public function delete(User $user, ?Content $resource = null): bool
    {
        if ($user->hasPermissionTo('advertise_statistic.moderate')) {
            return true;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return $user->hasPermissionTo('advertise_statistic.delete');
    }

    public function deleteOwn(User $user, ?Content $resource = null): bool
    {
        if (!$user->hasPermissionTo('advertise_statistic.delete')) {
            return false;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return true;
    }
}
