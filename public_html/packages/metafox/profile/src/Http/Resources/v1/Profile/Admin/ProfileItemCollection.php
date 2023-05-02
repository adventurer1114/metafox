<?php

namespace MetaFox\Profile\Http\Resources\v1\Profile\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * class ProfileItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class ProfileItemCollection extends ResourceCollection
{
    public $collects = ProfileItem::class;
}
