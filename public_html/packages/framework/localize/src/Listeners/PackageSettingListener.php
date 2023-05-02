<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Localize\Listeners;

use MetaFox\Platform\Support\BasePackageSettingListener;

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

    public function getEvents(): array
    {
        return [
            'packages.installed' => [
                PackageInstalledListener::class,
            ],
            'packages.deleted'   => [
                PackageDeletedListener::class,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'disable_translation' => [
                'config_name' => 'localize.disable_translation',
                'env_var'     => 'MFOX_DISABLE_TRANSLATION',
                'value'       => false,
            ],
            'default_locale'      => [
                'config_name' => 'app.locale',
                'env_var'     => 'MFOX_SITE_LOCALE',
                'value'       => 'en',
            ],
            'default_timezone'    => [
                'env_var'     => 'MFOX_SITE_TIMEZONE',
                'value'       => 'UTC',
            ],
        ];
    }
}
