<?php

namespace MetaFox\User\Http\Resources\v1\UserGender\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserGenderItemCollection extends ResourceCollection
{
    public $collects = UserGenderItem::class;
}
