<?php

namespace MetaFox\Photo\Policies;

use MetaFox\Photo\Models\Photo;
use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;

/**
 * Class PhotoGroupPolicy.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PhotoGroupPolicy implements ResourcePolicyInterface
{
    use HasPolicyTrait;

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if ($user->hasPermissionTo('photo_set.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('photo_set.view')) {
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

        return true;
    }

    public function view(User $user, Entity $resource): bool
    {
        if ($user->hasPermissionTo('photo_set.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('photo_set.view')) {
            return false;
        }

        $owner = $resource->owner;

        if (!$owner instanceof User) {
            return false;
        }
        if (!$resource instanceof PhotoGroup) {
            return false;
        }

        // Check can view on resource.
        if (PrivacyPolicy::checkPermission($user, $resource) == false) {
            return false;
        }

        // Check setting view on resource.
        /** @var PhotoPolicy $photoPolicy */
        $photoPolicy = PolicyGate::getPolicyFor(Photo::class);
        $statistic   = $resource->statistic?->toAggregateData();
        $totalPhoto  = $statistic['total_photo'];

        if ($totalPhoto == $resource->items()->count()) {
            return $photoPolicy->viewOwner($user, $owner);
        }

        return true;
    }

    public function create(User $user, $owner = null): bool
    {
        if (!$user->hasPermissionTo('photo_set.create')) {
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
            }
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('photo_set.moderate')) {
            return true;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return $user->hasPermissionTo('photo_set.update');
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('photo_set.moderate')) {
            return true;
        }

        return $this->deleteOwn($user, $resource);
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$user->hasPermissionTo('photo_set.delete')) {
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
