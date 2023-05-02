<?php

namespace MetaFox\Activity\Http\Resources\v1\Snooze;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SnoozeItemCollection extends ResourceCollection
{
    public $collects = SnoozeItem::class;
}
