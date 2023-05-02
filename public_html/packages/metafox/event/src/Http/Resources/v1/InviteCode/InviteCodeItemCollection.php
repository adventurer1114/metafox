<?php

namespace MetaFox\Event\Http\Resources\v1\InviteCode;

use Illuminate\Http\Resources\Json\ResourceCollection;

class InviteCodeItemCollection extends ResourceCollection
{
    public $collects = InviteCodeItem::class;
}
