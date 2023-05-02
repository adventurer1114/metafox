<?php

namespace MetaFox\StaticPage\Http\Resources\v1\StaticPage\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class StaticPageItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class StaticPageItemCollection extends ResourceCollection
{
    public $collects = StaticPageItem::class;
}
