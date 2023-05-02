<?php

namespace MetaFox\Search\Http\Resources\v1\Search;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SearchItemCollection extends ResourceCollection
{
    public $collects = SearchItem::class;

    public function toArray($request)
    {
        $items = parent::toArray($request);

        if (is_array($items)) {
            $items = collect($items);
        }

        return $items->filter(function ($item) {
            return is_array($item) && count($item) > 0;
        })->all();
    }
}
