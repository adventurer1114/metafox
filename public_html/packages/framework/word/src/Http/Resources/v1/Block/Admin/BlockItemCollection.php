<?php

namespace MetaFox\Word\Http\Resources\v1\Block\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * |--------------------------------------------------------------------------
 * | Resource Pattern
 * |--------------------------------------------------------------------------
 * | stub: /packages/resources/item_collection.stub.
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
