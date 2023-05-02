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

    public function checkPermissionIfExists($permission, $guardName = 'api'): bool
    {
        try {
            return $this->hasPermissionTo($permission, $guardName);
        } catch (Exception $e) {
            return true;
        }
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

        $permissionModel = $this->getPermissionClass()->findByName($permission, $guardName);

        if (!$permissionModel instanceof Permission) {
            throw new PermissionDoesNotExist();
        }

        $value = $this->hasPermissionViaRole($permissionModel);

        $this->getPermissionRegistrar()->setPermissionViaRole($this, $permission, $value);

        return $value;
    }

    public function roleId(): int
    {
        return $this->getRole()?->id ?? 0;
    }
}
