<?php

namespace MetaFox\Search\Http\Resources\v1\Search;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SuggestionItemCollection extends ResourceCollection
{
    public $collects = SuggestionItem::class;
}
