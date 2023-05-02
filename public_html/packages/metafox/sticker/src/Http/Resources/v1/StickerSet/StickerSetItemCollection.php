<?php

namespace MetaFox\Sticker\Http\Resources\v1\StickerSet;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StickerSetItemCollection extends ResourceCollection
{
    public $collects = StickerSetItem::class;
}
