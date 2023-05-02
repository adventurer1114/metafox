<?php

namespace MetaFox\Saved\Http\Resources\v1\SavedListMember;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MemberItemCollection extends ResourceCollection
{
    public $collects = MemberItem::class;
}
