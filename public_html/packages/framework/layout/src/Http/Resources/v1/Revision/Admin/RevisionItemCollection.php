<?php

namespace MetaFox\Layout\Http\Resources\v1\Revision\Admin;

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
class RevisionItemCollection extends ResourceCollection
{
    public $collects = RevisionItem::class;
}
