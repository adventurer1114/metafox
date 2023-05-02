<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Music\Http\Resources\v1\Music;

use MetaFox\Music\Support\Browse\Scopes\Song\SortScope;
use MetaFox\Music\Support\Browse\Scopes\Song\ViewScope;
use MetaFox\Platform\Resource\MobileSetting as Setting;
use MetaFox\Platform\Support\Browse\Scopes\WhenScope;

/**
 *--------------------------------------------------------------------------
 * Music Mobile Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class MobileSetting.
 * @ignore
 * @codeCoverageIgnore
 */
class MobileSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('viewAll')
            ->apiUrl('music/search')
            ->apiRules([
                'q'    => ['truthy', 'q'],
                'sort' => [
                    'includes', 'sort', SortScope::getAllowSort(),
                ],
                'when' => [
                    'includes', 'when', WhenScope::getAllowWhen(),
                ],
                'view' => [
                    'includes', 'view', ViewScope::getAllowView(),
                ],
                'genre_id'    => ['truthy', 'genre_id'],
                'entity_type' => ['truthy', 'entity_type'],
            ]);
    }
}
