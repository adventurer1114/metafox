<?php

namespace MetaFox\Forum\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use MetaFox\Forum\Models\Forum;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;

/**
 * Class ForumPolicy.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ForumPolicy implements ResourcePolicyInterface
{
    use HandlesAuthorization;

    protected string $type = 'forum';

    public function create(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('admincp.has_admin_access')) {
            return false;
        }

        return true;
    }

    public function viewAdminCP(User $user): bool
    {
        if (!$user->hasPermissionTo('admincp.has_admin_access')) {
            return false;
        }

        return $this->viewAny($user);
    }

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('forum.view')) {
            return false;
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if (!$user->hasPermissionTo('admincp.has_admin_access')) {
            return false;
        }

        return true;
    }

    public function view(User $user, Entity $resource): bool
    {
        if (!$user->hasPermissionTo('forum.view')) {
            return false;
        }

        if ($resource instanceof Forum) {
            return true;
        }

        return false;
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if (!$user->hasPermissionTo('admincp.has_admin_access')) {
            return false;
        }

        return true;
    }

    public function viewOwner(User $user, ?User $owner = null): bool
    {
        return true;
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        return true;
    }

    public function viewOnProfilePage(User $user, User $owner): bool
    {
        return true;
    }
}
