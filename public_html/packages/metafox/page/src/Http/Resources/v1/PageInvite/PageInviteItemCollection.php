<?php

namespace MetaFox\Page\Http\Resources\v1\PageInvite;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PageInviteItemCollection extends ResourceCollection
{
    public $collects = PageInviteItem::class;
}
