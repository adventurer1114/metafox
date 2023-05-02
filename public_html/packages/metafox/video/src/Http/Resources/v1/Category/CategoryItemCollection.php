<?php

namespace MetaFox\Video\Http\Resources\v1\Category;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryItemCollection extends ResourceCollection
{
    public $collects = CategoryItem::class;
}
