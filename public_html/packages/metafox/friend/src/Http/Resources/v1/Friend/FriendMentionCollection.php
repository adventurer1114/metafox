<?php

namespace MetaFox\Friend\Http\Resources\v1\Friend;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FriendMentionCollection extends ResourceCollection
{
    public $collects = FriendMentionItem::class;
}
