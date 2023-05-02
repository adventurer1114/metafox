<?php

namespace MetaFox\Activity\Http\Resources\v1\Feed;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FeedItemCollection extends ResourceCollection
{
    public $collects = FeedItem::class;
}
