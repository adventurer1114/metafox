<?php

namespace MetaFox\Friend\Http\Resources\v1\FriendRequest;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RequestSentCollection extends ResourceCollection
{
    public $collects = RequestSentItem::class;
}
