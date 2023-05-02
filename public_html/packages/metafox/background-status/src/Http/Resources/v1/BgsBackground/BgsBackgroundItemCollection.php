<?php

namespace MetaFox\BackgroundStatus\Http\Resources\v1\BgsBackground;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BgsBackgroundItemCollection extends ResourceCollection
{
    public bool $preserveKeys = true;
    public $collects          = BgsBackgroundItem::class;
}
