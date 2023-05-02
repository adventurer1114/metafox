<?php

namespace MetaFox\Blog\Http\Resources\v1\Category;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class CategoryItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class CategoryItemCollection extends ResourceCollection
{
    /**
     * @var string
     */
    protected string $collect = CategoryItem::class;
}
