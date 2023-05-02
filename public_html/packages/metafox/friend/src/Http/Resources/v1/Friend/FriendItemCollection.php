<?php

namespace MetaFox\Friend\Http\Resources\v1\Friend;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FriendItemCollection extends ResourceCollection
{
    public $collects = FriendItem::class;
}
