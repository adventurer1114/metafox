<?php

namespace MetaFox\Group\Http\Resources\v1\Member;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class MemberItemCollection.
 */
class MemberItemCollection extends ResourceCollection
{
    public $collects = MemberItem::class;
}
