<?php

namespace MetaFox\Forum\Http\Resources\v1\Forum;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ForumSideBlockItemCollection extends ResourceCollection
{
    /**
     * @var string
     */
    public $collects = ForumSideBlockItem::class;
}
