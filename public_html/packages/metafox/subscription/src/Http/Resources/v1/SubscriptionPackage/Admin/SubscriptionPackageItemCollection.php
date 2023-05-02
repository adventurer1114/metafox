<?php

namespace MetaFox\Subscription\Http\Resources\v1\SubscriptionPackage\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class SubscriptionPackageItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class SubscriptionPackageItemCollection extends ResourceCollection
{
    public $collects = SubscriptionPackageItem::class;
}
