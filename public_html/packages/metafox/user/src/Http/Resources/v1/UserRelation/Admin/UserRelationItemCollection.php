<?php

namespace MetaFox\User\Http\Resources\v1\UserRelation\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserRelationItemCollection extends ResourceCollection
{
    public $collects = UserRelationItem::class;
}
