<?php

namespace MetaFox\Announcement\Http\Resources\v1\AnnouncementView;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AnnouncementViewItemCollection extends ResourceCollection
{
    public $collects = AnnouncementViewItem::class;
}
