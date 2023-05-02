<?php

namespace MetaFox\Advertise\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;

/**
 * stub: /packages/policies/model_policy.stub.
 */

/**
 * Class SponsorPolicy.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class SponsorPolicy implements ResourcePolicyInterface
{
    use HandlesAuthorization;
    use HasPolicyTrait;

    protected string $type = 'advertise_sponsor';

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('advertise_sponsor.view')) {
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

        //if (UserPrivacy::hasAccess($user, $owner, 'advertise_sponsor.view_browse_blogs') == false) {
        //    return false;
        //}

        return true;
    }

    public function view(User $user, Entity $resource): bool
    {
        if (!$user->hasPermissionTo('advertise_sponsor.view')) {
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
        if (!$user->hasPermissionTo('advertise_sponsor.create')) {
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

                //if (!UserPrivacy::hasAccess($user, $owner, 'advertise_sponsor.share_blogs')) {
                //    return false;
                //}
            }
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('advertise_sponsor.moderate')) {
            return true;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return $user->hasPermissionTo('advertise_sponsor.update');
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('advertise_sponsor.moderate')) {
            return true;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return $user->hasPermissionTo('advertise_sponsor.delete');
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$user->hasPermissionTo('advertise_sponsor.delete')) {
            return false;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return true;
    }

    public function payment(User $user, Entity $entity): bool
    {
        /*
         * TODO: implement role setting for payment
         */

        if ($user->entityId() != $entity->userId()) {
            return false;
        }

        return true;
    }
}
