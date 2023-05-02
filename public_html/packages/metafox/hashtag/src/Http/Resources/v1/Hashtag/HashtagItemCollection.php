<?php

namespace MetaFox\Hashtag\Http\Resources\v1\Hashtag;

use Illuminate\Http\Resources\Json\ResourceCollection;

class HashtagItemCollection extends ResourceCollection
{
    public $collects = HashtagItem::class;
}
