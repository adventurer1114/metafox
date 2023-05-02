<?php

namespace MetaFox\Video\Support;

use MetaFox\Video\Contracts\Support\VideoSupportInterface;
use MetaFox\Video\Repositories\VideoRepositoryInterface;

class VideoSupport implements VideoSupportInterface
{
    private VideoRepositoryInterface $repository;

    public function __construct(VideoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function deleteVideoByAssetId(string $assetId): bool
    {
        return $this->repository->deleteVideoByAssetId($assetId);
    }
}
