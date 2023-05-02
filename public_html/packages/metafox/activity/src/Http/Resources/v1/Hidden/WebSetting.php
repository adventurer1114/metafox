<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Activity\Http\Resources\v1\Hidden;

use MetaFox\Platform\Resource\WebSetting as Setting;

/**
 *--------------------------------------------------------------------------
 * Hidden Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class WebSetting.
 */
class WebSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('unhideItem')
            ->apiUrl('feed/hide-all/:id?item_type=:module_name')
            ->asDelete();

        $this->add('undoUnhideItem')
            ->apiUrl('feed/hide-all/:id?item_type=:module_name')
            ->asPost();
    }
}
