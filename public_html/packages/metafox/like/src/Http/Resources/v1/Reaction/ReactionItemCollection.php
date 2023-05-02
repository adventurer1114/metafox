<?php

namespace MetaFox\Like\Http\Resources\v1\Reaction;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReactionItemCollection extends ResourceCollection
{
    public $collects = ReactionItem::class;
}
