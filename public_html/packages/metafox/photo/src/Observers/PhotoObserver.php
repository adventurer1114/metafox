<?php

namespace MetaFox\Photo\Observers;

use Exception;
use Illuminate\Support\Carbon;
use MetaFox\Photo\Models\Album;
use MetaFox\Photo\Models\Photo;
use MetaFox\Photo\Repositories\AlbumRepositoryInterface;
use MetaFox\Photo\Repositories\PhotoGroupRepositoryInterface;
use MetaFox\Photo\Repositories\PhotoRepositoryInterface;
use MetaFox\Platform\Contracts\HasAvatar;
use MetaFox\Platform\Contracts\HasCover;
use MetaFox\Platform\Contracts\HasUserProfile;
use MetaFox\User\Models\UserProfile;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PhotoObserver
{
    /**
     * @return AlbumRepositoryInterface
     */
    private function albumRepository(): AlbumRepositoryInterface
    {
        return resolve(AlbumRepositoryInterface::class);
    }

    public function creating(Photo $photo): void
    {
        $this->updateAlbumType($photo);
    }

    public function created(Photo $photo): void
    {
        if ($photo->album_id > 0) {
            $album = $photo->album;

            if ($album->cover_photo_id == 0) {
                $this->albumRepository()->updateAlbumCover($album, $photo->entityId());
            }
        }
    }

    public function updating(Photo $photo): void
    {
        $this->updateAlbumType($photo);
    }

    public function updated(Photo $photo): void
    {
        if ($photo->isDirty(['album_id'])) {
            $this->updateAlbumCover($photo);
        }

        if ($photo->isDirty(['is_approved'])) {
            if (!$photo->group_id) {
                return;
            }

            if ($photo->isApproved()) {
                if (null === $photo->group) {
                    return;
                }

                app('events')->dispatch('photo.group.increase_total_item', [$photo->group, $photo->entityType()], true);
            }
        }
    }

    /**
     * @throws Exception
     */
    public function deleted(Photo $photo): void
    {
        resolve(PhotoRepositoryInterface::class)->cleanUpRelationData($photo);

        if ($photo->group_id > 0) {
            resolve(PhotoGroupRepositoryInterface::class)->updateApprovedStatus($photo->group_id);
        }
    }

    /**
     * @param Photo $photo
     */
    private function updateAlbumType(Photo $photo): void
    {
        if ($photo->album_id > 0) {
            $album = $photo->album;

            if (null !== $album) {
                $photo->album_type = $album->album_type;
            }
        }
    }

    /**
     * @param Photo $photo
     */
    private function updateAlbumCover(Photo $photo): void
    {
        $oldAlbumId = $photo->getOriginal('album_id');

        if ($oldAlbumId > 0) {
            $oldAlbum        = $this->albumRepository()->find($oldAlbumId);
            $isOldAlbumCover = $oldAlbum->cover_photo_id == $photo->entityId();
            if ($isOldAlbumCover) {
                $this->albumRepository()->updateAlbumCover($oldAlbum);
            }
        }
    }
}
