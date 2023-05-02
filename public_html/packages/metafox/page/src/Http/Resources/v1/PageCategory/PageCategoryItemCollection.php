<?php

namespace MetaFox\Page\Http\Resources\v1\PageCategory;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PageCategoryItemCollection extends ResourceCollection
{
    public $collects = PageCategoryItem::class;
}
