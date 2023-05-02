<?php

namespace MetaFox\Marketplace\Http\Resources\v1\Listing;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ListingItemCollection extends ResourceCollection
{
    public $collects = ListingItem::class;
}
