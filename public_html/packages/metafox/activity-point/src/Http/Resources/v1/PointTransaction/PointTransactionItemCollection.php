<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointTransaction;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class PointTransactionItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class PointTransactionItemCollection extends ResourceCollection
{
    public $collects = PointTransactionItem::class;
}
