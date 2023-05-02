<?php

namespace MetaFox\Authorization\Repositories\Contracts;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use MetaFox\Authorization\Models\Permission;
use MetaFox\Authorization\Models\Role;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * Interface Permission.
 * @mixin AbstractRepository
 */
interface PermissionRepositoryInterface
{
    /**
     * @param User $context
     * @param int  $id
     *
     * @return Permission
     */
    public function viewPermission(User $context, int $id): Permission;

    /**
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @return Paginator
     */
    public function viewPermissions(User $context, array $attributes): Paginator;

    /**
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @return Collection
     */
    public function getPermissionsForEdit(User $context, array $attributes): Collection;

    /**
     * @param  User                 $context
     * @param  Role                 $role
     * @param  array<string, mixed> $params
     * @return bool
     */
    public function updatePermissionValue(User $context, Role $role, array $params): bool;
}
