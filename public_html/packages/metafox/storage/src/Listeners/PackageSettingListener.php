<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Storage\Listeners;

use MetaFox\Platform\Support\BasePackageSettingListener;

class PackageSettingListener extends BasePackageSettingListener
{

    public function getPolicies(): array
    {
        return [
        ];
    }

    public function getUserPermissions(): array
    {
        return [];
    }

    public function getSiteSettings(): array
    {
        // check for installation only.
        $data = app('files')->getRequire(base_path('config/filesystems.php'));
        // load original config
        $disks = $data['disks'] ?? [];

        $settings = [
            'filesystems.default'             => [
                'value'     => $data['default'] ?? 'public',
                'is_public' => 0,
            ],
            'filesystems.max_upload_filesize' => [
                'value'     => config('upload.filesize'),
                'is_public' => 1,
            ],
        ];

        if (is_array($disks)) {
            foreach ($disks as $key => $values) {
                $name = sprintf('filesystems.disks.%s', $key);

                $settings[$name] = [
                    'value'     => $values,
                    'is_auto'   => 1,
                    'is_public' => 0,
                ];
            }
        }

        return $settings;
    }

    public function getEvents(): array
    {
        return [
            'models.notify.created' => [
                ModelCreatedListener::class,
            ],
            'packages.installed'    => [
                PackageInstalledListener::class,
            ],
        ];
    }
}
