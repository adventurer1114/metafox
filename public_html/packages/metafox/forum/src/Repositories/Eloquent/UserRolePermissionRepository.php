<?php

namespace MetaFox\Forum\Repositories\Eloquent;

use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Forum\Repositories\UserRolePermissionRepositoryInterface;
use MetaFox\Forum\Models\UserRolePermission;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class UserRolePermissionRepository.
 */
class UserRolePermissionRepository extends AbstractRepository implements UserRolePermissionRepositoryInterface
{
    public function model()
    {
        return UserRolePermission::class;
    }
}
