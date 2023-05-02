<?php

namespace MetaFox\Forum\Http\Resources\v1\ForumPost;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

class ForumPostItemCollection extends ResourceCollection
{
    /**
     * @var string
     */
    public $collects = ForumPostItem::class;
}
