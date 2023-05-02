<?php

namespace MetaFox\Group\Http\Resources\v1\Group;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupSimpleCollection extends ResourceCollection
{
    public $collects = GroupSimple::class;
}
