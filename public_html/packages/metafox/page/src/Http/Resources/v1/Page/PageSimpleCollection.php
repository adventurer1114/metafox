<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PageSimpleCollection extends ResourceCollection
{
    public $collects = PageSimple::class;
}
