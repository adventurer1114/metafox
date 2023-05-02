<?php

namespace MetaFox\Blog\Http\Resources\v1\Blog;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BlogItemCollection extends ResourceCollection
{
    protected $collect = BlogItem::class;
}
