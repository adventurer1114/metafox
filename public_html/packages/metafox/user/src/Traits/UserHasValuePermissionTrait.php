<?php

namespace MetaFox\User\Traits;

use Illuminate\Database\Eloquent\Collection;
use MetaFox\Authorization\Models\Permission;
use MetaFox\Authorization\Models\Role;
use MetaFox\Authorization\Traits\RoleHasValuePermissionTrait;

/**
 * @property Collection|Role[] $roles
 */
trait UserHasValuePermissionTrait
{
    use RoleHasValuePermissionTrait;

    public function hasValuePermissionViaRole(Permission $permission): bool
    {
        return $this->hasRole($permission->rolesHasValuePermissions);
    }

    public function getRole(): ?Role
    {
        return $this->roles->first();
    }

    public function hasPermissionValue($permission): bool
    {
        $permission = $this->findPermissionValue($permission);

        return $this->hasValuePermissionViaRole($permission);
    }
}
