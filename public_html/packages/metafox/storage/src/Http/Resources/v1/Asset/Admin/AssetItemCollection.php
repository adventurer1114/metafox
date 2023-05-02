<?php

namespace MetaFox\Storage\Http\Resources\v1\Asset\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class AssetItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class AssetItemCollection extends ResourceCollection
{
    public $collects = AssetItem::class;
}
