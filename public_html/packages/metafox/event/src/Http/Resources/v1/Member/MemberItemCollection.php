<?php

namespace MetaFox\Event\Http\Resources\v1\Member;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MemberItemCollection extends ResourceCollection
{
    public $collects = MemberItem::class;
}
