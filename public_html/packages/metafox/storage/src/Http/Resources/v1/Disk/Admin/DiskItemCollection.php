<?php

namespace MetaFox\Storage\Http\Resources\v1\Disk\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class DiskItemCollection extends ResourceCollection
{
    protected string $collect = DiskItem::class;
}
