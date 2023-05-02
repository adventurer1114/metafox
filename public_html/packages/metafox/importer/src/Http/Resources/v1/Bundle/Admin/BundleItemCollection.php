<?php

namespace MetaFox\Importer\Http\Resources\v1\Bundle\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * |--------------------------------------------------------------------------
 * | Resource Pattern
 * |--------------------------------------------------------------------------
 * | stub: /packages/resources/item_collection.stub
 */

/**
 * Class BundleItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class BundleItemCollection extends ResourceCollection
{
    public $collects = BundleItem::class;
}
