<?php

namespace MetaFox\Comment\Http\Resources\v1\CommentHistory;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentHistoryCollection extends ResourceCollection
{
    public $collects = CommentHistoryItem::class;
}
