<?php

namespace MetaFox\Video\Contracts;

use Illuminate\Http\Request;
use MetaFox\Storage\Models\StorageFile;

/**
 * class VideoServiceInterface.
 */
interface VideoServiceInterface
{
    /**
     * @param  StorageFile          $file
     * @return array<string, mixed>
     */
    public function processVideo(StorageFile $file): array;

    public function handleWebhook(Request $request): bool;

    public function getProviderType(): string;
}
