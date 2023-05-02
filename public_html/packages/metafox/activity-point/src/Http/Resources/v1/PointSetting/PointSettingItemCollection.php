<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointSetting;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class PointSettingItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class PointSettingItemCollection extends ResourceCollection
{
    public $collects = PointSettingItem::class;
}
