<?php

namespace MetaFox\QuotaControl\Http\Resources\v1\Admin;

use MetaFox\Authorization\Http\Resources\v1\Permission\Admin\EditPermissionForm as Form;
use MetaFox\Authorization\Repositories\Contracts\PermissionRepositoryInterface;

class EditPermissionForm extends Form
{
    /**
     * booted.
     *
     * @return void
     */
    protected function booted()
    {
        $permissionRepository = resolve(PermissionRepositoryInterface::class);

        $this->permissions = $permissionRepository->getPermissionsForEdit(user(), [
            'actions' => ['quota_control'],
        ]);
    }
}
