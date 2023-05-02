<?php

namespace MetaFox\Profile\Http\Resources\v1\Section\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class SectionItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class SectionItemCollection extends ResourceCollection
{
    public $collects = SectionItem::class;
}
