<?php

namespace MetaFox\Video\Support\Facade;

use Illuminate\Support\Facades\Facade;
use MetaFox\Video\Contracts\Support\VideoSupportInterface;

/**
 * @method static bool deleteVideoByAssetId(string $assetId)
 *
 * @see VideoSupport
 */
class Video extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return VideoSupportInterface::class;
    }
}
