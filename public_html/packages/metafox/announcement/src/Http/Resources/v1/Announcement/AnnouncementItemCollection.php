<?php

namespace MetaFox\Announcement\Http\Resources\v1\Announcement;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AnnouncementItemCollection extends ResourceCollection
{
    public $collects = AnnouncementItem::class;
}
