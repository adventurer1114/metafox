<?php

namespace MetaFox\Importer\Http\Resources\v1\Entry\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * |--------------------------------------------------------------------------
 * | Resource Pattern
 * |--------------------------------------------------------------------------
 * | stub: /packages/resources/item_collection.stub
 */

/**
 * Class EntryItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class EntryItemCollection extends ResourceCollection
{
    public $collects = EntryItem::class;
}
