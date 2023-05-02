<?php

namespace MetaFox\Authorization\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Artisan;
use InvalidArgumentException;
use MetaFox\Authorization\Models\Permission;
use MetaFox\Authorization\Models\Role;
use MetaFox\Authorization\Policies\PermissionPolicy;
use MetaFox\Authorization\Repositories\Contracts\PermissionRepositoryInterface;
use MetaFox\Authorization\Support\Browse\Scopes\Permission\FilterByRoleScope;
use MetaFox\Authorization\Support\Browse\Scopes\Permission\ModuleScope;
use MetaFox\Authorization\Support\Browse\Scopes\Permission\SortScope;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxDataType;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\Platform\UserRole;

/**
 * Class BlogRepository.
 * @property Permission $model
 * @method   Permission getModel()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PermissionRepository extends AbstractRepository implements PermissionRepositoryInterface
{
    public function model(): string
    {
        return Permission::class;
    }

    /**
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @return Paginator
     * @throws AuthorizationException
     * @Todo: Should be removed if this method is not used
     */
    public function viewPermissions(User $context, array $attributes): Paginator
    {
        policy_authorize(PermissionPolicy::class, 'viewAny', $context);

        $limit    = $attributes['limit'];
        $module   = $attributes['module_name'];
        $sortType = $attributes['sort_type'];
        $search   = $attributes['q'];
        $roleId   = (int) ($attributes['role']);

        $query = $this->getModel()->newModelInstance()->newQuery();

        // Apply role filter
        if ($roleId) {
            $roleScope = new FilterByRoleScope();
            $roleScope->setRoleId($roleId);
            $query = $query->addScope($roleScope);
        }

        // Apply module filter
        if ($module) {
            $moduleScope = new ModuleScope();
            $moduleScope->setModuleId($module);
            $query = $query->addScope($moduleScope);
        }

        if ($search != '') {
            $query = $query->addScope(new SearchScope($search, ['name']));
        }

        $sortScope = new SortScope();
        $sortScope->setSortType($sortType);

        return $query->addScope($sortScope)
            ->simplePaginate($limit);
    }

    /**
     * @param User $context
     * @param int  $id
     *
     * @return Permission
     * @throws AuthorizationException
     */
    public function viewPermission(User $context, int $id): Permission
    {
        $permission = $this->find($id);

        policy_authorize(PermissionPolicy::class, 'view', $context);

        return $permission;
    }

    /**
     * @throws AuthorizationException
     */
    public function getPermissionsForEdit(User $context, array $attributes): Collection
    {
        policy_authorize(PermissionPolicy::class, 'viewAny', $context);

        $query = $this->getModel()->newModelInstance()
            ->newQuery()
            ->where('is_public', '=', 1)
            ->whereNot('entity_type', '*');

        if (!empty($attributes['exclude_actions'])) {
            $query = $query->whereNotIn('action', $attributes['exclude_actions']);
        }

        if (!empty($attributes['module_id'])) {
            $query = $query->where('module_id', $attributes['module_id']);
        }

        if (!empty($attributes['actions'])) {
            $query = $query->whereIn('action', $attributes['actions']);
        }

        if ($context->hasRole(UserRole::NORMAL_USER)) {
            $query->where('require_admin', 0)
                ->where('require_staff', 0);
        }

        if ($context->hasRole(UserRole::STAFF_USER)) {
            $query->where('require_admin', 0);
        }

        return $query
            ->orderBy('id')
            ->get();
    }

    /**
     * @param  User                   $context
     * @param  Role                   $role
     * @param  array<string, mixed>   $params
     * @return bool
     * @throws AuthorizationException
     */
    public function updatePermissionValue(User $context, Role $role, array $params): bool
    {
        policy_authorize(PermissionPolicy::class, 'update', $context);

        foreach ($params as $name => $value) {
            $permission = $this->findByName($name);
            match ($permission->data_type) {
                MetaFoxDataType::BOOLEAN => $this->updatePermissionValueAsBoolean($role, $permission, $value),
                default                  => $this->updatePermissionValueAsInteger($role, $permission, $value),
            };
        }

        Artisan::call('cache:reset');

        return true;
    }

    public function findByName(string $name): Permission
    {
        $permission = $this->getModel()
            ->newModelQuery()
            ->where('name', $name)
            ->first();

        if (!$permission instanceof Permission) {
            throw (new ModelNotFoundException())->setModel(Permission::class);
        }

        return $permission;
    }

    protected function updatePermissionValueAsBoolean(Role $role, Permission $permission, mixed $value): bool
    {
        if (MetaFoxDataType::BOOLEAN !== $permission->data_type) {
            return false;
        }

        $value = (bool) $value;

        if ($value) {
            $role->givePermissionTo($permission);
        }

        if (!$value) {
            $role->revokePermissionTo($permission);
        }

        return true;
    }

    protected function updatePermissionValueAsInteger(Role $role, Permission $permission, mixed $value): bool
    {
        if (MetaFoxDataType::BOOLEAN === $permission->data_type) {
            throw new InvalidArgumentException('Invalid data type');
        }

        if (!is_numeric($value)) {
            throw new InvalidArgumentException('value is not numeric');
        }

        // No change occurs => skip update
        if ($value === $role->getPermissionValue($permission)) {
            return false;
        }

        $role->valuePermissions()
            ->newPivotQuery()
            ->updateOrInsert(
                [
                    'role_id'       => $role->entityId(),
                    'permission_id' => $permission->entityId(),
                ],
                ['value' => (int) $value]
            );

        return true;
    }
}
