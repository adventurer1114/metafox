<?php

namespace MetaFox\Page\Http\Resources\v1\PageCategory\Admin;

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
