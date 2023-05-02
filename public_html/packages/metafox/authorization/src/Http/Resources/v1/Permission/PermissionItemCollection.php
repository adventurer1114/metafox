<?php

namespace MetaFox\Authorization\Http\Resources\v1\Permission;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PermissionItemCollection extends ResourceCollection
{
    public $collects = PermissionItem::class;
}
