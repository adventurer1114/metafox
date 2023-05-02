<?php

namespace MetaFox\Importer\Http\Resources\v1\Log\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * |--------------------------------------------------------------------------
 * | Resource Pattern
 * |--------------------------------------------------------------------------
 * | stub: /packages/resources/item_collection.stub
 */

/**
 * Class LogItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class LogItemCollection extends ResourceCollection
{
    public $collects = LogItem::class;
}
