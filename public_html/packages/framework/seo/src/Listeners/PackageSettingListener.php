<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\SEO\Listeners;

use MetaFox\Platform\MetaFoxConstant;
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
        ];
    }

    public function getCheckers(): array
    {
        return [
            \MetaFox\SEO\Checks\CheckFacebookCrawlerReachable::class,
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'sitemap_exclude_types' => ['value' => [], 'is_public' => false],
        ];
    }
}
