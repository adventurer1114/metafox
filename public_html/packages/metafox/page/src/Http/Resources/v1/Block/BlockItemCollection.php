<?php

namespace MetaFox\Page\Http\Resources\v1\Block;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * |--------------------------------------------------------------------------
 * | Resource Pattern
 * |--------------------------------------------------------------------------
 * | stub: /packages/resources/item_collection.stub
 */

/**
 * Class BlockItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class BlockItemCollection extends ResourceCollection
{
    public $collects = BlockItem::class;
}
