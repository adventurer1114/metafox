<?php

namespace MetaFox\Subscription\Http\Resources\v1\SubscriptionInvoiceTransaction;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class SubscriptionInvoiceTransactionCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class SubscriptionInvoiceTransactionCollection extends ResourceCollection
{
    public $collects = SubscriptionInvoiceTransactionItem::class;
}
