<?php

namespace MetaFox\Saved\Policies;

use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\Saved\Models\Saved;
use MetaFox\Saved\Models\SavedList;

/**
 * Class SavedPolicy.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 */
class SavedPolicy implements ResourcePolicyInterface
{
    use HasPolicyTrait;

    protected string $type = Saved::class;

    /**
     * @inerhitDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function viewAny(User $user, ?User $owner = null): bool
    {
        return true;
    }

    public function view(User $user, Entity $resource): bool
    {
        if (!$resource instanceof Saved) {
            return false;
        }

        if ($user->entityId() != $resource->userId()) {
            return false;
        }

        return true;
    }

    /**
     * @inerhitDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(User $user, ?User $owner = null, ?string $entityType = null): bool
    {
        if (!$user->hasPermissionTo('saved.create')) {
            return false;
        }

        if (null !== $entityType) {
            return $user->hasPermissionTo($entityType . '.save');
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if (!$this->deleteOwn($user, $resource)) {
            return false;
        }

        return true;
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if (!$this->deleteOwn($user, $resource)) {
            return false;
        }

        return true;
    }

    /**
     * @inerhitDoc
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function viewOwner(User $user, ?User $owner = null): bool
    {
        return false;
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$resource instanceof Saved) {
            return false;
        }

        if ($user->entityId() != $resource->userId()) {
            return false;
        }

        return true;
    }

    public function removeItemFromCollection(User $user, ?Content $savedList = null, ?Entity $saved = null): bool
    {
        if (!$savedList instanceof SavedList) {
            return false;
        }

        if ($user->hasSuperAdminRole()) {
            return true;
        }

        if ($user->entityId() == $savedList->userId()) {
            return true;
        }

        if (!$saved instanceof Saved) {
            return false;
        }

        if ($user->entityId() != $saved->userId()) {
            return false;
        }

        return true;
    }
}
