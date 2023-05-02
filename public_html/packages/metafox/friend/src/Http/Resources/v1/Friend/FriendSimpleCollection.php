<?php

namespace MetaFox\Friend\Http\Resources\v1\Friend;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FriendSimpleCollection extends ResourceCollection
{
    public $collects = FriendSimple::class;
}
