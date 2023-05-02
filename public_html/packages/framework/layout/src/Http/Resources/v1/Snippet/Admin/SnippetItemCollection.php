<?php

namespace MetaFox\Layout\Http\Resources\v1\Snippet\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * |--------------------------------------------------------------------------
 * | Resource Pattern
 * |--------------------------------------------------------------------------
 * | stub: /packages/resources/item_collection.stub.
 */

/**
 * Class SnippetItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class SnippetItemCollection extends ResourceCollection
{
    public $collects = SnippetItem::class;
}
