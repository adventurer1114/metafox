<?php

namespace MetaFox\SEO\Http\Resources\v1\Meta\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * |--------------------------------------------------------------------------
 * | Resource Pattern
 * |--------------------------------------------------------------------------
 * | stub: /packages/resources/item_collection.stub.
 */

/**
 * Class MetaItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class MetaItemCollection extends ResourceCollection
{
    public $collects = MetaItem::class;
}
