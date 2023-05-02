<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PackageTransaction;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * |--------------------------------------------------------------------------
 * | Resource Pattern
 * |--------------------------------------------------------------------------
 * | stub: /packages/resources/item_collection.stub
 */

/**
 * Class PackageTransactionItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class PackageTransactionItemCollection extends ResourceCollection
{
    public $collects = PackageTransactionItem::class;
}
