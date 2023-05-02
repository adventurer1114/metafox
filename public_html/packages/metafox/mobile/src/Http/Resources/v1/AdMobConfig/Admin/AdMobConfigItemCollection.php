<?php

namespace MetaFox\Mobile\Http\Resources\v1\AdMobConfig\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class AdMobConfigItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class AdMobConfigItemCollection extends ResourceCollection
{
    public $collects = AdMobConfigItem::class;
}
