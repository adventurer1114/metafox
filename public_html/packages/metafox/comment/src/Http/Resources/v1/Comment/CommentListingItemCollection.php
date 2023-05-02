<?php

namespace MetaFox\Comment\Http\Resources\v1\Comment;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentListingItemCollection extends ResourceCollection
{
    public $collects = CommentListingItem::class;
}
