<?php

namespace MetaFox\Core\Http\Resources\v1\AdminAccess;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AdminAccessItemCollection extends ResourceCollection
{
    protected string $collect = AdminAccessItem::class;
}
