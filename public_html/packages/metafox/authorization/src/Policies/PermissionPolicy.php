<?php

namespace MetaFox\Authorization\Policies;

use MetaFox\Authorization\Models\Permission;
use MetaFox\Platform\Contracts\User as User;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Models\User as Model;

/**
 * Class PermissionPolicy.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PermissionPolicy
{
    use HasPolicyTrait;

    protected string $type = Permission::class;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User|Model $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        if (!$user->hasPermissionTo('user_permission.manage')) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view a model.
     *
     * @param  User|Model $user
     * @return bool
     */
    public function view(User $user): bool
    {
        if (!$user->hasPermissionTo('user_permission.manage')) {
            return false;
        }

        return true;
    }
}
