<?php

namespace MetaFox\Sticker\Observers;

use MetaFox\Sticker\Models\StickerRecent;
use MetaFox\Sticker\Models\StickerUserValue;

/**
 * Class StickerUserValueObserver.
 * @ignore
 * @codeCoverageIgnore
 */
class StickerUserValueObserver
{
    public function deleted(StickerUserValue $stickerUserValue): void
    {
        $stickerIds = $stickerUserValue->stickerSet->stickers()->get(['id'])->pluck('id')->toArray();

        StickerRecent::query()
            ->where('user_id', $stickerUserValue->userId())
            ->whereIn('sticker_id', $stickerIds)
            ->delete();
    }
}
