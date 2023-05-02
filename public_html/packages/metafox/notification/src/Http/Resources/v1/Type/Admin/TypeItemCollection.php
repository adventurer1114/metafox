<?php

namespace MetaFox\Notification\Http\Resources\v1\Type\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class TypeItemCollection extends ResourceCollection
{
    public $collects = TypeItem::class;
}
