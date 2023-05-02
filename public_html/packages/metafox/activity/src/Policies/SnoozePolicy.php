<?php

namespace MetaFox\Activity\Policies;

use MetaFox\Activity\Models\Snooze as Resource;
use MetaFox\Platform\Contracts\User as User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;

class SnoozePolicy
{
    use HasPolicyTrait;

    protected string $type = Resource::class;

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('activity_snooze.view')) {
            return false;
        }

        if ($owner) {
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

        return true;
    }

    public function view(User $user, Resource $resource): bool
    {
        // check user role permission
        if (!$user->hasPermissionTo('activity_snooze.view')) {
            return false;
        }

        if ($resource->user_id != $user->entityId()) {
            return false;
        }

        return true;
    }

    public function create(User $user, User $owner): bool
    {
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

    public function update(User $user, Resource $resource): bool
    {
        if ($user->hasPermissionTo('activity_snooze.moderate')) {
            return true;
        }

        return $this->updateOwn($user, $resource);
    }

    public function updateOwn(User $user, ?Resource $resource = null): bool
    {
        if (!$user->hasPermissionTo('activity_snooze.update')) {
            return false;
        }

        if ($resource instanceof Resource) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return true;
    }

    public function delete(User $user, ?Resource $resource = null): bool
    {
        if ($user->hasPermissionTo('activity_snooze.moderate')) {
            return true;
        }

        return $this->deleteOwn($user, $resource);
    }

    public function deleteOwn(User $user, ?Resource $resource = null): bool
    {
        if (!$user->hasPermissionTo('activity_snooze.delete')) {
            return false;
        }

        if ($resource instanceof Resource) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return true;
    }
}
