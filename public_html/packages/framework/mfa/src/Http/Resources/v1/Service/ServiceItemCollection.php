<?php

namespace MetaFox\Mfa\Http\Resources\v1\Service;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class ServiceItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class ServiceItemCollection extends ResourceCollection
{
    public $collects = ServiceItem::class;
}
