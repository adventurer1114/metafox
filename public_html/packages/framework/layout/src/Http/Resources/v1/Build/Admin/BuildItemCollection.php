<?php

namespace MetaFox\Layout\Http\Resources\v1\Build\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class BuildItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class BuildItemCollection extends ResourceCollection
{
    public $collects = BuildItem::class;
}
