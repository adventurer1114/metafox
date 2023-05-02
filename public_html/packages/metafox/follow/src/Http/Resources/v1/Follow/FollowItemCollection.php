<?php

namespace MetaFox\Follow\Http\Resources\v1\Follow;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class FollowItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class FollowItemCollection extends ResourceCollection
{
    public $collects = FollowItem::class;
}
