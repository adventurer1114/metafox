<?php

namespace MetaFox\Group\Http\Resources\v1\Mute;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class MuteItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class MuteItemCollection extends ResourceCollection
{
    public $collects = MuteItem::class;
}
