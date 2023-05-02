<?php

namespace MetaFox\Event\Http\Resources\v1\Event;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EventItemCollection extends ResourceCollection
{
    public $collects = EventItem::class;
}
