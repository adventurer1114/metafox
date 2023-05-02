<?php

namespace MetaFox\User\Http\Resources\v1\UserEntity;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserEntityItemCollection extends ResourceCollection
{
    public $collects = UserEntityItem::class;
}
