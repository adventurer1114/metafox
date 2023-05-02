<?php

namespace MetaFox\Activity\Http\Resources\v1\ActivityHistory;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class ActivityHistoryItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class ActivityHistoryItemCollection extends ResourceCollection
{
    public $collects = ActivityHistoryItem::class;
}
