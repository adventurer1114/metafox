<?php

namespace MetaFox\Search\Http\Resources\v1\Search;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SearchGroupItemCollection extends ResourceCollection
{
    public $collects = SearchGroupItem::class;
}
