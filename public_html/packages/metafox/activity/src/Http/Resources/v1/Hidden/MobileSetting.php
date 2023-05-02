<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Activity\Http\Resources\v1\Hidden;

use MetaFox\Platform\Resource\MobileSetting as Setting;

/**
 * Class MobileSetting.
 */
class MobileSetting extends Setting
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
