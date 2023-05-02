<?php

namespace MetaFox\Page\Http\Resources\v1\PageMember;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PageMemberItemCollection extends ResourceCollection
{
    public $collects = PageMemberItem::class;
}
