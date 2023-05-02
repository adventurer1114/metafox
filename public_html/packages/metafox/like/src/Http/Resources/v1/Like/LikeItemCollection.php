<?php

namespace MetaFox\Like\Http\Resources\v1\Like;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LikeItemCollection extends ResourceCollection
{
    public $collects = LikeItem::class;
}
