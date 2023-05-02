<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Music\Http\Resources\v1\Music;

use MetaFox\Form\Constants;
use MetaFox\Music\Models\Song;
use MetaFox\Music\Support\Browse\Scopes\Song\SortScope;
use MetaFox\Music\Support\Browse\Scopes\Song\ViewScope;
use MetaFox\Music\Support\Facades\Music;
use MetaFox\Platform\Resource\WebSetting as ResourceSetting;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\WhenScope;

/**
 *--------------------------------------------------------------------------
 * Song Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class WebSetting.
 * @preload
 */
class WebSetting extends ResourceSetting
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
