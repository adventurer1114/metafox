<?php

namespace MetaFox\ActivityPoint\Policies;

use MetaFox\ActivityPoint\Models\PointPackage as Resource;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;

/**
 * Point Package Policy.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PackagePolicy
{
    use HasPolicyTrait;

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if ($this->moderate($user)) {
            return true;
        }

        if (!$user->hasPermissionTo('activitypoint.can_purchase_points')) {
            return false;
        }

        if ($user->hasPermissionTo('activitypoint_package.view')) {
            return true;
        }

        return false;
    }

    public function view(User $user, Resource $resource): bool
    {
        return $this->viewAny($user);
    }

    public function viewOwner(User $user, ?User $owner = null): bool
    {
        return true;
    }

    public function create(User $user, ?User $owner = null): bool
    {
        if ($this->moderate($user)) {
            return true;
        }

        return $user->hasPermissionTo('activitypoint_package.create');
    }

    public function update(User $user, ?Resource $resource = null): bool
    {
        if ($this->moderate($user)) {
            return true;
        }

        return $user->hasPermissionTo('activitypoint_package.update');
    }

    public function delete(User $user, ?Resource $resource = null): bool
    {
        if ($this->moderate($user)) {
            return true;
        }

        return $user->hasPermissionTo('activitypoint_package.delete');
    }

    public function deleteOwn(User $user, ?Resource $resource = null): bool
    {
        return false;
    }

    public function purchase(User $user, Resource $resource): bool
    {
        if (!$resource->is_active) {
            return false;
        }

        if ($user->hasPermissionTo('activitypoint_package.moderate')) {
            return true;
        }

        if ($user->hasPermissionTo('activitypoint.can_purchase_points')) {
            return true;
        }

        return false;
    }

    public function moderate(User $user): bool
    {
        return $user->hasPermissionTo('activitypoint_package.moderate');
    }
}
