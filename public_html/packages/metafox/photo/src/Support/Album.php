<?php

namespace MetaFox\Photo\Support;

use MetaFox\Photo\Contracts\AlbumContract;
use MetaFox\Photo\Models\Album as ModelsAlbum;
use MetaFox\Photo\Repositories\AlbumRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Contracts\UserEntity;

/**
 * Class Album.
 */
class Album implements AlbumContract
{
    public function __construct(protected AlbumRepositoryInterface $repository)
    {
    }

    /**
     * @return array
     */
    public function getDefaultTypes(): array
    {
        return [
            ModelsAlbum::COVER_ALBUM,
            ModelsAlbum::PROFILE_ALBUM,
            ModelsAlbum::TIMELINE_ALBUM,
        ];
    }

    /**
     * @param  int|null $value
     * @return bool
     */
    public function isDefaultAlbum(?int $value): bool
    {
        if (null === $value) {
            return false;
        }

        return in_array($value, $this->getDefaultTypes());
    }

    /**
     * @param ModelsAlbum $album
     *
     * @return string
     */
    public function getDefaultAlbumTitle(ModelsAlbum $album): string
    {
        $name        = $album->name;
        $ownerEntity = $album->ownerEntity;
        if ($ownerEntity instanceof UserEntity) {
            $name = __p($name, ['full_name' => $ownerEntity->name]);
        }

        return $name;
    }

    /**
     * @param  User       $context
     * @param  string     $userType
     * @param  int        $userId
     * @return mixed|void
     */
    public function chunkingTrashedAlbums(User $context, string $userType, int $userId)
    {
        $this->repository->chunkingTrashedAlbums($context, $userType, $userId);
    }
}
