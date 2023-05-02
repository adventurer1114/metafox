<?php

namespace MetaFox\Broadcast\Http\Resources\v1\Connection\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class ConnectionItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class ConnectionItemCollection extends ResourceCollection
{
    public $collects = ConnectionItem::class;
}
