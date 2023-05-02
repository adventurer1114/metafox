<?php

namespace MetaFox\Group\Http\Resources\v1\Invite;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class InviteItemCollection.
 */
class InviteItemCollection extends ResourceCollection
{
    public $collects = InviteItem::class;
}
