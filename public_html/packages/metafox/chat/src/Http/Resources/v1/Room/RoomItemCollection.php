<?php

namespace MetaFox\Chat\Http\Resources\v1\Room;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoomItemCollection extends ResourceCollection
{
    protected $collect = RoomItem::class;
}
