<?php

namespace MetaFox\Blog\Http\Resources\v1\Category\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class CategoryItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class CategoryItemCollection extends ResourceCollection
{
    protected string $collect = CategoryItem::class;
}
