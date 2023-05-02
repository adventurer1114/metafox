<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointStatistic\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class PointStatisticItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class PointStatisticItemCollection extends ResourceCollection
{
    public $collects = PointStatisticItem::class;
}
