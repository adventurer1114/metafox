<?php

namespace MetaFox\Photo\Support\Facades;

use Illuminate\Support\Facades\Facade;
use MetaFox\Photo\Contracts\AlbumContract;
use MetaFox\Platform\Contracts\User;

/**
 * @method static bool isDefaultAlbum(int $value)
 * @method static void chunkingTrashedAlbums(User $context, string $userType, int $userId)
 */
class Album extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AlbumContract::class;
    }
}
