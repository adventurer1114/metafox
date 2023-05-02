<?php

namespace MetaFox\Comment\Http\Resources\v1\Comment;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentItemRelatedCollection extends ResourceCollection
{
    public $collects = CommentItemRelated::class;
}
