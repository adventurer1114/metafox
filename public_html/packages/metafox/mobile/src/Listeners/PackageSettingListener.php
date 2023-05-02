<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Mobile\Listeners;

use MetaFox\Platform\Support\BasePackageSettingListener;

/**
 * Class PackageSettingListener.
 * @SuppressWarnings(PHPMD)
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getSiteSettings(): array
    {
        return [
            'admob_banner_uid.android' => [
                'type'  => 'string',
                'value' => '',
            ],
            'admob_banner_uid.ios' => [
                'type'  => 'string',
                'value' => '',
            ],
            'admob_interstitial_uid.android' => [
                'type'  => 'string',
                'value' => '',
            ],
            'admob_interstitial_uid.ios' => [
                'type'  => 'string',
                'value' => '',
            ],
            'admob_rewarded_uid.android' => [
                'type'  => 'string',
                'value' => '',
            ],
            'admob_rewarded_uid.ios' => [
                'type'  => 'string',
                'value' => '',
            ],
        ];
    }

    public function getEvents(): array
    {
        return [
            'packages.installed' => [
                PackageInstalledListener::class,
            ],
        ];
    }
}
