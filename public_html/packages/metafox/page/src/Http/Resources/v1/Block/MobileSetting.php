<?php

namespace MetaFox\Page\Http\Resources\v1\Block;

use MetaFox\Platform\Resource\MobileSetting as ResourceSetting;

/**
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */
class MobileSetting extends ResourceSetting
{
    protected function initialize(): void
    {
        $this->add('viewAll')
            ->apiUrl('page-block')
            ->apiParams(['page_id' => ':id']);

        $this->add('unblockFromPage')
            ->apiUrl('page-unblock')
            ->asDelete()
            ->apiParams(['page_id' => ':page_id', 'user_id' => ':user_id']);
    }
}
