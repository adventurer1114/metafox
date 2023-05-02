<?php

namespace MetaFox\Authorization\Http\Resources\v1\Role\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleItemCollection extends ResourceCollection
{
    public $collects = RoleItem::class;
}
