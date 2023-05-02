<?php

namespace MetaFox\Comment\Http\Resources\v1\Comment;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentItemCollection extends ResourceCollection
{
    public $collects = CommentItem::class;
}
