<?php

namespace MetaFox\Layout\Http\Resources\v1\Theme\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
*/

/**
 * Class ThemeItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class ThemeItemCollection extends ResourceCollection
{
    public $collects = ThemeItem::class;
}
