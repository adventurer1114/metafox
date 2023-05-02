<?php

namespace MetaFox\BackgroundStatus\Http\Resources\v1\RecentUsed;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RecentUsedItemCollection extends ResourceCollection
{
    public $collects = RecentUsedItem::class;
}
