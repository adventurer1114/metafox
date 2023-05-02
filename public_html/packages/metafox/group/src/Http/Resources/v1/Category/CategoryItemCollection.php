<?php

namespace MetaFox\Group\Http\Resources\v1\Category;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class CategoryItemCollection.
 */
class CategoryItemCollection extends ResourceCollection
{
    public $collects = CategoryItem::class;
}
