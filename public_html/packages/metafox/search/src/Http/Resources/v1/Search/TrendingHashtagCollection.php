<?php

namespace MetaFox\Search\Http\Resources\v1\Search;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TrendingHashtagCollection extends ResourceCollection
{
    public $collects = TrendingHashtagItem::class;
}
