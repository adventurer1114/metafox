<?php

namespace MetaFox\Sticker\Http\Resources\v1\StickerSet\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class StickerSetItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class StickerSetItemCollection extends ResourceCollection
{
    public $collects = StickerSetItem::class;
}
