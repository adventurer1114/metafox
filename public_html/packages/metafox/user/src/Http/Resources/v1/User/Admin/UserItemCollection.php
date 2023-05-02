<?php

namespace MetaFox\User\Http\Resources\v1\User\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserItemCollection extends ResourceCollection
{
    public $collects = UserItem::class;
}
