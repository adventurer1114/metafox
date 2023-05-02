<?php

namespace MetaFox\Advertise\Http\Resources\v1\Sponsor\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class SponsorItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class SponsorItemCollection extends ResourceCollection
{
    public $collects = SponsorItem::class;
}
