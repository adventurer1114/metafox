<?php

namespace MetaFox\Forum\Http\Resources\v1\ForumThread;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SearchSuggestionCollection extends ResourceCollection
{
    /**
     * @var string
     */
    public $collects = SearchSuggestionItem::class;
}
