<?php

namespace MetaFox\Sticker\Http\Resources\v1\Sticker;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StickerItemCollection extends ResourceCollection
{
    public $collects = StickerItem::class;
}
