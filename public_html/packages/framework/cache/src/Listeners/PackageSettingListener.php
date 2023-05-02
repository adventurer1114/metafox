<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Cache\Listeners;

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
    private function injectCacheStoreConfig(array &$settings): void
    {
        $settings['default'] = [
            'config_name' => 'cache.default',
            'env_var'     => 'MFOX_CACHE_DRIVER',
            'value'       => 'file',
            'type'        => 'string',
            'is_public'   => 0,
        ];
    }

    public function getSiteSettings(): array
    {
        $settings = [];

        $this->injectCacheStoreConfig($settings);

        return $settings;
    }
}
