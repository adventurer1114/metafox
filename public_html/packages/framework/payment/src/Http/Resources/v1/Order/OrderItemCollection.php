<?php

namespace MetaFox\Payment\Http\Resources\v1\Order;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * |--------------------------------------------------------------------------
 * | Resource Pattern
 * |--------------------------------------------------------------------------
 * | stub: /packages/resources/item_collection.stub.
 */

/**
 * Class OrderItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class OrderItemCollection extends ResourceCollection
{
    public $collects = OrderItem::class;
}
