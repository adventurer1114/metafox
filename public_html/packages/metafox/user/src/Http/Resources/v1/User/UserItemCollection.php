<?php

namespace MetaFox\User\Http\Resources\v1\User;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserItemCollection extends ResourceCollection
{
    public $collects = UserItem::class;
}
