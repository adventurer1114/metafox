<?php

namespace MetaFox\Core\Http\Resources\v1\Link;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LinkItemCollection extends ResourceCollection
{
    public $collects = LinkItem::class;
}
