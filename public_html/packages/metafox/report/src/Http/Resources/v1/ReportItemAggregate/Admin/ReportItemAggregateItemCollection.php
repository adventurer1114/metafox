<?php

namespace MetaFox\Report\Http\Resources\v1\ReportItemAggregate\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class ReportItemAggregateItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class ReportItemAggregateItemCollection extends ResourceCollection
{
    public $collects = ReportItemAggregateItem::class;
}
