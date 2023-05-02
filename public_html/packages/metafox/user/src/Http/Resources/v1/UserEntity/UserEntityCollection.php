<?php

namespace MetaFox\User\Http\Resources\v1\UserEntity;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserEntityCollection extends ResourceCollection
{
    public $collects = UserEntityDetail::class;
}
