<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Mobile\Http\Resources\v1;

use MetaFox\Mobile\Http\Resources\v1\AdMobConfig\Admin\AdMobConfigItemCollection;
use MetaFox\Mobile\Models\AdMobConfig;
use MetaFox\Mobile\Models\AdMobPage;
use MetaFox\Mobile\Repositories\AdMobConfigAdminRepositoryInterface;
use MetaFox\Mobile\Repositories\AdMobPageAdminRepositoryInterface;
use MetaFox\Platform\Contracts\User;

/**
 * Class PackageSetting.
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSetting
{
    public function getWebSettings(): array
    {
        return [];
    }

    public function getMobileSettings(): array
    {
        $context = user();

        return [
            'admob_configs' => $this->getAdMobConfigs($context),
        ];
    }

    protected function getAdMobConfigs(User $context): array
    {
        $data = resolve(AdMobPageAdminRepositoryInterface::class)->getConfigForSettings($context);

        return $data->keyBy('path')->map(function (AdMobPage $page) {
            return new AdMobConfigItemCollection($page->configs);
        })->toArray();
    }
}
