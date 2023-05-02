<?php

namespace MetaFox\Friend\Http\Resources\v1\FriendRequest;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PendingCollection extends ResourceCollection
{
    public $collects = PendingItem::class;
}
