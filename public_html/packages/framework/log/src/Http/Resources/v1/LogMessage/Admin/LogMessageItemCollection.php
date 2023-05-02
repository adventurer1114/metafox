<?php

namespace MetaFox\Log\Http\Resources\v1\LogMessage\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * |--------------------------------------------------------------------------
 * | Resource Pattern
 * |--------------------------------------------------------------------------
 * | stub: /packages/resources/item_collection.stub.
 */

/**
 * Class LogMessageItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class LogMessageItemCollection extends ResourceCollection
{
    public $collects = LogMessageItem::class;
}
