<?php

namespace MetaFox\Poll\Http\Resources\v1\Poll;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PollItemCollection extends ResourceCollection
{
    public $collects = PollItem::class;
}
