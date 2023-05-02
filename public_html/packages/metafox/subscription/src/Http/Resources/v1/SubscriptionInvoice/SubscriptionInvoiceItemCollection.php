<?php

namespace MetaFox\Subscription\Http\Resources\v1\SubscriptionInvoice;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class SubscriptionInvoiceItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class SubscriptionInvoiceItemCollection extends ResourceCollection
{
    public $collects = SubscriptionInvoiceItem::class;
}
