<?php

namespace MetaFox\Profile\Http\Resources\v1\Structure\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * class StructureItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class StructureItemCollection extends ResourceCollection
{
    public $collects = StructureItem::class;
}
