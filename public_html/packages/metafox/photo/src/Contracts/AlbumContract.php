<?php

namespace MetaFox\Photo\Contracts;

use MetaFox\Photo\Models\Album;
use MetaFox\Platform\Contracts\User;

interface AlbumContract
{
    /**
     * @return array
     */
    public function getDefaultTypes(): array;

    /**
     * @param  int|null $value
     * @return bool
     */
    public function isDefaultAlbum(?int $value): bool;

    /**
     * @param Album $album
     *
     * @return string
     */
    public function getDefaultAlbumTitle(Album $album): string;

    /**
     * @param  User   $context
     * @param  string $userType
     * @param  int    $userId
     * @return mixed
     */
    public function chunkingTrashedAlbums(User $context, string $userType, int $userId);
}
