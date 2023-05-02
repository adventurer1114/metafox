<?php

namespace MetaFox\Blog\Policies;

use MetaFox\Blog\Models\Category;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;

/**
 * Class CategoryPolicy.
 * @ignore
 * @codeCoverageIgnore
 */
class CategoryPolicy implements ResourcePolicyInterface
{
    use HasPolicyTrait;

    protected string $type = Category::class;

    // Check can view on owner.

    public function viewActive(?Entity $category): bool
    {
        return (bool) $category?->is_active;
    }

    public function viewAny(User $user, ?User $owner = null): bool
    {
        return true;
    }

    public function view(User $user, Entity $resource): bool
    {
        return true;
    }

    public function viewOwner(User $user, User $owner): bool
    {
        return true;
    }

    public function create(User $user, ?User $owner = null): bool
    {
        return $user->hasPermissionTo('admincp.has_admin_access');
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        return $user->hasPermissionTo('admincp.has_admin_access');
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        return $user->hasPermissionTo('admincp.has_admin_access');
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        return $user->hasPermissionTo('admincp.has_admin_access');
    }
}
