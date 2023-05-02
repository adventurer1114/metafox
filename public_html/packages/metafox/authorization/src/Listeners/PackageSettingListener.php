<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Authorization\Listeners;

use MetaFox\Authorization\Models\Permission;
use MetaFox\Authorization\Models\Role;
use MetaFox\Authorization\Policies\PermissionPolicy;
use MetaFox\Authorization\Policies\RolePolicy;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;

/**
 * --------------------------------------------------------------------------
 * Code Generator
 * --------------------------------------------------------------------------
 * stub: src/Listeners/PackageSettingListener.stub.
 */

/**
 * Class PackageSettingListener.
 * @SuppressWarnings(PHPMD)
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getUserPermissions(): array
    {
        return [
            Role::ENTITY_TYPE       => [
                'manage' => UserRole::LEVEL_ADMINISTRATOR,
            ],
            Permission::ENTITY_TYPE => [
                'manage' => UserRole::LEVEL_ADMINISTRATOR,
            ],
        ];
    }

    public function getPolicies(): array
    {
        return [
            Role::class       => RolePolicy::class,
            Permission::class => PermissionPolicy::class,
        ];
    }
}
