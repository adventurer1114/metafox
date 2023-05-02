<?php

namespace MetaFox\Authorization\Repositories\Eloquent;

use Illuminate\Support\Arr;
use MetaFox\Authorization\Models\Permission;
use MetaFox\Authorization\Models\Role;
use MetaFox\Authorization\Repositories\PermissionSettingRepositoryInterface;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxDataType;
use MetaFox\Platform\ModuleManager;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\UserRole;

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class PermissionSettingRepository extends AbstractRepository implements PermissionSettingRepositoryInterface
{
    public function model()
    {
        return Permission::class;
    }

    public function installSettingsFromApps(): bool
    {
        $response = ModuleManager::instance()->discoverSettings('getUserPermissions');

        if (!is_array($response) || empty($response)) {
            return false;
        }

        foreach ($response as $moduleId => $settings) {
            if (empty($settings) || !is_string($moduleId)) {
                continue;
            }

            $this->installSettings($moduleId, $settings);
        }

        return true;
    }

    public function installSettings(string $moduleId, array $resourceSettings): bool
    {
        foreach ($resourceSettings as $entityType => $settings) {
            if (empty($settings) || !is_string($entityType)) {
                continue;
            }

            foreach ($settings as $permissionName => $roles) {
                $params = [
                    'name'          => sprintf('%s.%s', $entityType, $permissionName),
                    'guard_name'    => Role::DEFAULT_GUARD,
                    'module_id'     => $moduleId,
                    'entity_type'   => $entityType,
                    'action'        => $permissionName,
                    'default_value' => $settings['default'] ?? null,
                    'data_type'     => $settings['type'] ?? MetaFoxDataType::BOOLEAN,
                    'is_public'     => 1,
                ];

                /** @var Permission $permission */
                $permission = Permission::query()->where([
                    'name'       => sprintf('%s.%s', $entityType, $permissionName),
                    'guard_name' => Role::DEFAULT_GUARD,
                    'module_id'  => $moduleId,
                ])->firstOrNew();

                $permission->fill($params);
                $permission->save();

                if (!empty($roles)) {
                    $permission->assignRole($roles);
                }
            }
        }

        return true;
    }

    public function installValueSettingsFromApps(): bool
    {
        $response = ModuleManager::instance()->discoverSettings('getUserValuePermissions');

        if (!is_array($response) || empty($response)) {
            return false;
        }

        foreach ($response as $moduleId => $settings) {
            if (empty($settings) || !is_string($moduleId)) {
                continue;
            }

            $this->installValueSettings($moduleId, $settings);
        }

        return true;
    }

    public function installValueSettings(string $moduleId, array $resourceSettings): bool
    {
        foreach ($resourceSettings as $entityType => $settings) {
            if (empty($settings) || !is_string($entityType)) {
                continue;
            }

            foreach ($settings as $permissionName => $setting) {
                $values = [
                    'entity_type'   => $entityType,
                    'action'        => $permissionName,
                    'data_type'     => $setting['type'] ?? MetaFoxDataType::INTEGER,
                    'default_value' => $setting['default'] ?? null,
                    'is_public'     => Arr::get($setting, 'is_public', 1),
                    'extra'         => $setting['extra'] ?? null,
                ];

                /** @var Permission $permission */
                $permission = Permission::query()->updateOrCreate([
                    'name'       => sprintf('%s.%s', $entityType, $permissionName),
                    'guard_name' => Role::DEFAULT_GUARD,
                    'module_id'  => $moduleId,
                ], $values);

                if (!empty($setting['roles'])) {
                    $permission->assignRoleWithPivot($setting['roles']);
                }
            }
        }

        return true;
    }

    public function getPermissions(Role $role): array
    {
        $collection = $this->getModel()->newInstance()
            ->newQuery()
            ->whereIn('module_id', resolve('core.packages')->getActivePackageAliases())
            ->where('is_public', MetaFoxConstant::IS_PUBLIC)
            ->get()
            ->groupBy('entity_type');

        $data = [];

        foreach ($collection as $entityType => $permissions) {
            /** @var Permission[] $permissions */
            foreach ($permissions as $permission) {
                $data[$permission->module_id][$entityType][$permission->action] = match ($permission->data_type) {
                    MetaFoxDataType::BOOLEAN => $role->hasPermissionTo($permission->name),
                    MetaFoxDataType::INTEGER => (int) $role->getPermissionValue($permission->name),
                    default                  => $role->getPermissionValue($permission->name)
                };
            }
        }

        Arr::set($data, 'user.user.isLoggedIn', $role->entityId() !== UserRole::GUEST_USER);
        Arr::set($data, 'user.user.isGuest', $role->entityId() === UserRole::GUEST_USER);

        return $data;
    }

    public function getExcludedActions(): array
    {
        return ['flood_control', 'quota_control'];
    }

    public function rollDownPermissions(string $moduleId, array $notIn): void
    {
        Permission::query()
            ->where('module_id', $moduleId)
            ->whereNotIn('name', $notIn)
            ->update(['is_public' => 0]);
    }
}
