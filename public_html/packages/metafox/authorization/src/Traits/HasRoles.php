<?php

namespace MetaFox\Authorization\Traits;

use Exception;
use MetaFox\User\Contracts\PermissionRegistrar;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

trait HasRoles
{
    use \Spatie\Permission\Traits\HasRoles;

    public function getPermissionRegistrar(): PermissionRegistrar
    {
        return app(PermissionRegistrar::class);
    }

    public function hasPermissionTo($permission, $guardName = 'api'): bool
    {
        if (!is_string($permission)) {
            abort(500, 'MetaFox does not support permission not STRING');
        }

        if (config('permission.enable_wildcard_permission', false)) {
            return $this->hasWildcardPermission($permission, $guardName);
        }

        $getFromCache = $this->getPermissionRegistrar()->getPermissionViaRole($this, $permission);

        if ($getFromCache !== null) {
            return $getFromCache;
        }

        try {
            $permissionModel = $this->resolvePermissionModel($permission, $guardName);
            if (!$permissionModel instanceof Permission) {
                throw new PermissionDoesNotExist();
            }

            $value = $this->hasPermissionViaRole($permissionModel);

            $this->getPermissionRegistrar()->setPermissionViaRole($this, $permission, $value);

            return $value;
        } catch (Exception) {
            $this->getPermissionRegistrar()->setPermissionViaRole($this, $permission, false);
        }

        return false;
    }

    public function roleId(): int
    {
        return $this->getRole()?->id ?? 0;
    }

    protected function resolvePermissionModel(string $permission, $guardName = 'api')
    {
        try {
            return $this->getPermissionClass()->findByName($permission, $guardName);
        } catch (Exception) {
            // try to resolve wildcard permission
            return $this->getPermissionClass()->findByWildcardName($permission, $guardName);
        }
    }
}
