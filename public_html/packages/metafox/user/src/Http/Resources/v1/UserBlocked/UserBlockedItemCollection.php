<?php

namespace MetaFox\User\Http\Resources\v1\UserBlocked;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserBlockedItemCollection extends ResourceCollection
{
    public $collects = UserBlockedItem::class;
}
