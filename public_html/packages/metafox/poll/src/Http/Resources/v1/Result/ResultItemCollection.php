<?php

namespace MetaFox\Poll\Http\Resources\v1\Result;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ResultItemCollection extends ResourceCollection
{
    public $collects = ResultItem::class;
}
