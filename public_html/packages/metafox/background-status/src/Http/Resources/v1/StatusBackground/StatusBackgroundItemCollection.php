<?php

namespace MetaFox\BackgroundStatus\Http\Resources\v1\StatusBackground;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StatusBackgroundItemCollection extends ResourceCollection
{
    public $collects = StatusBackgroundItem::class;
}
