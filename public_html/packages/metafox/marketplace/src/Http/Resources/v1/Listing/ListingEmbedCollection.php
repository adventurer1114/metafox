<?php

namespace MetaFox\Marketplace\Http\Resources\v1\Listing;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ListingEmbedCollection extends ResourceCollection
{
    /** @var string */
    protected $collect = ListingEmbed::class;
}
