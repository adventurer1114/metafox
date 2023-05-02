<?php

namespace MetaFox\Event\Http\Resources\v1\Invite;

use Illuminate\Http\Resources\Json\ResourceCollection;

class InviteItemCollection extends ResourceCollection
{
    public $collects = InviteItem::class;
}
