<?php

namespace MetaFox\Like\Policies;

use MetaFox\Like\Models\Like;
use MetaFox\Platform\Contracts\ActionEntity;
use MetaFox\Platform\Contracts\ActionOnResourcePolicyInterface;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;

/**
 * Class LikePolicy.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 */
class LikePolicy implements ActionOnResourcePolicyInterface
{
    use HasPolicyTrait;

    protected string $type = Like::ENTITY_TYPE;

    public function getEntityType(): string
    {
        return Like::ENTITY_TYPE;
    }

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('like.view')) {
            return false;
        }

        if ($owner instanceof User) {
            if (!$this->viewOwner($user, $owner)) {
                return false;
            }
        }

        return true;
    }

    public function view(User $user, ?Entity $resource): bool
    {
        // check user role permission
        if (!$user->hasPermissionTo('like.view')) {
            return false;
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

    public function create(User $user, ?Content $resource = null): bool
    {
        if (!$user->hasPermissionTo('like.create')) {
            return false;
        }

        if (!$resource instanceof HasTotalLike) {
            return false;
        }

        $entityPermission = "{$resource->entityType()}.like";

        if (!$user->hasPermissionTo($entityPermission)) {
            return false;
        }

        $owner = $resource->owner;
        if ($owner->entityId() != $user->entityId()) {
            if (!PrivacyPolicy::checkCreateOnOwner($user, $owner)) {
                return false;
            }
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        return false;
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        return $this->deleteOwn($user, $resource);
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if ($resource instanceof ActionEntity) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return $user->hasPermissionTo('like.create');
    }
}
