<?php

namespace MetaFox\Group\Http\Resources\v1\Group;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupItemCollection extends ResourceCollection
{
    public $collects = GroupItem::class;
}
