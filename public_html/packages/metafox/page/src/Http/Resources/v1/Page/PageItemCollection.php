<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PageItemCollection extends ResourceCollection
{
    public $collects = PageItem::class;
}
