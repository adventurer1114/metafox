<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointPackage\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class PointPackageItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class PointPackageItemCollection extends ResourceCollection
{
    public $collects = PointPackageItem::class;
}
