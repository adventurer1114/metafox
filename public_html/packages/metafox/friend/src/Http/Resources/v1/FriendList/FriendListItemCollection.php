<?php

namespace MetaFox\Friend\Http\Resources\v1\FriendList;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FriendListItemCollection extends ResourceCollection
{
    public $collects = FriendListItem::class;
}
