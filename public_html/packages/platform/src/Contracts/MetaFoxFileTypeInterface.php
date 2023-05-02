<?php

namespace MetaFox\Platform\Contracts;

use Illuminate\Http\UploadedFile;

interface MetaFoxFileTypeInterface
{
    public function isAllowType(string $type): bool;

    /**
     * @param  string $type
     * @return string
     */
    public function getMimeTypeFromType(string $type): string;

    public function verifyMime(UploadedFile $file, string $type): bool;

    /**
     * This method return how many bytes should a type can be uploaded to server.
     *
     * @param  string $type
     * @return int
     */
    public function getFilesizePerType(string $type): int;

    /**
     * @param  string $type
     * @return float
     */
    public function getFilesizeInMegabytes(string $type): float;

    /**
     * @param  string|null $mimeType
     * @return string|null
     */
    public function getTypeByMime(?string $mimeType): ?string;

    /**
     * @param  string $fileType
     * @return string
     */
    public function transformFileType(string $fileType): string;

    /**
     * @param int $bytes
     */
    public function getFilesizeReadableString(int $bytes): string;
}
