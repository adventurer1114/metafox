<?php

namespace MetaFox\Video\Contracts\Support;

interface VideoSupportInterface
{
    public function deleteVideoByAssetId(string $assetId): bool;
}
