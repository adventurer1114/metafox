<?php

namespace MetaFox\Core\Repositories;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use MetaFox\Authorization\Models\Role;
use MetaFox\Core\Constants;
use MetaFox\Core\Repositories\Contracts\AppSettingRepositoryInterface;
use MetaFox\Form\AbstractForm;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\PackageManager;
use MetaFox\Platform\Resource\Actions;

class AppSettingRepository implements AppSettingRepositoryInterface
{
    public function getMobileSettings(Request $request, Role $role): array
    {
        return [
            'forms'         => $this->loadForms('mobile'),
            'actions'       => $this->loadActions('mobile'),
            'appMenus'      => resolve('menu')->loadMenus('mobile', false),
            'resourceMenus' => resolve('menu')->loadMenus('mobile', true),
            'acl'           => app('perms')->getPermissions($role),
            'assets'        => app('asset')->loadAssetSettings(),
            'settings'      => Settings::getSiteSettings('mobile', true),
            'mainMenu'      => require(base_path('packages/metafox/core/resources/menu/mainMenu.php')),
        ];
    }

    public function getAdminSettings(Request $request, Role $role): array
    {
        return [
            'forms'         => $this->loadForms('admin'),
            'actions'       => $this->loadActions('admin'),
            'appMenus'      => resolve('menu')->loadMenus('admin', false),
            'resourceMenus' => resolve('menu')->loadMenus('admin', true),
            'acl'           => app('perms')->getPermissions($role),
            'assets'        => app('asset')->loadAssetSettings(),
            'settings'      => Settings::getSiteSettings('admin', true),
        ];
    }

    public function getWebSettings(Request $request, Role $role): array
    {
        return [
            'forms'         => $this->loadForms('web'),
            'actions'       => $this->loadActions('web'),
            'appMenus'      => resolve('menu')->loadMenus('web', false),
            'resourceMenus' => resolve('menu')->loadMenus('web', true),
            'acl'           => app('perms')->getPermissions($role),
            'assets'        => app('asset')->loadAssetSettings(),
            'settings'      => Settings::getSiteSettings('web', true),
        ];
    }

    /**
     * @param  string            $for web, mobile, admin
     * @return array<int, mixed>
     */
    public function loadActions(string $for): array
    {
        $results = [];

        $type = match ($for) {
            'mobile' => Constants::DRIVER_TYPE_RESOURCE_ACTIONS,
            default  => Constants::DRIVER_TYPE_RESOURCE_WEB,
        };

        $drivers = resolve(DriverRepositoryInterface::class)
            ->loadDrivers($type, $for);

        foreach ($drivers as $driver) {
            [$resourceName, $class, , $appName, $packageId, $alt] = $driver;

            if (!class_exists($class)) {
                continue;
            }
            $setting = new $class($appName, $resourceName);

            if (!$setting instanceof Actions) {
                continue;
            }

            $alias = PackageManager::getAliasFor($packageId, $for);

            $data = $setting->toArray();
            Arr::set($results, sprintf('%s.%s', $alias, $resourceName), $data);

            if ($alt) {
                Arr::set($results, sprintf('%s.%s', $alias, $alt), $data);
            }
        }

        return $results;
    }

    /**
     * @param  string               $for
     * @return array<string, mixed>
     */
    public function loadForms(string $for): array
    {
        $mobile = false;

        switch ($for) {
            case 'mobile':
                $mobile = true;
                break;

        }

        $results = [];
        $drivers = resolve(DriverRepositoryInterface::class)
            ->loadDrivers(Constants::DRIVER_TYPE_FORM, $for, true, null, true);

        foreach ($drivers as $driver) {
            try {
                [$name, $class, , , $packageId] = $driver;

                if (!class_exists($class)) {
                    continue;
                }

                /** @var AbstractForm $setting */
                $setting = new $class($name);

                $data  = $setting->toArray(request());
                $alias = PackageManager::getAliasFor($packageId, $for);

                Arr::set($results, "$alias.$name", $data);
            } catch (\Exception $exception) {
            }
        }

        return $results;
    }

    public function loadMorphMap()
    {
    }
}
