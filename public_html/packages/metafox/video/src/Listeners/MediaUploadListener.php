<?php

namespace MetaFox\Video\Listeners;

use Illuminate\Support\Arr;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\TempFileModel;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Video\Models\Video;
use MetaFox\Video\Policies\VideoPolicy;
use MetaFox\Video\Repositories\VideoRepositoryInterface;

class MediaUploadListener
{
    private VideoRepositoryInterface $repository;

    public function __construct(VideoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param  User                 $user
     * @param  User                 $owner
     * @param  string               $itemType
     * @param  TempFileModel        $file
     * @param  array<string, mixed> $params
     * @return Content|null|bool
     */
    public function handle(User $user, User $owner, string $itemType, TempFileModel $file, array $params)
    {
        if (Video::ENTITY_TYPE != $itemType) {
            return null;
        }

        if (!$this->canUploadToAlbum($user, $params)) {
            return false;
        }

        if (array_key_exists('categories', $params)) {
            unset($params['categories']);
        }

        return $this->repository->tempFileToVideo($user, $owner, $file, $params);
    }

    protected function canUploadToAlbum(User $user, array $params): bool
    {
        if (!Arr::has($params, 'album_id')) {
            return true;
        }

        $albumId = Arr::get($params, 'album_id', 0);

        $album = app('events')->dispatch('photo.album.get_by_id', [$albumId], true);

        if (null == $album) {
            return true;
        }

        if ($album?->is_timeline) {
            return true;
        }

        if ($album->is_normal) {
            //Disallow uploading videos when uploading photos is disabled
            if (!app('events')->dispatch('photo.album.can_upload_to_album', [$user, $album->owner, 'photo'], true)) {
                return false;
            }

            //Only working with normal album
            if (!Settings::get('photo.photo_allow_uploading_video_to_photo_album', true)) {
                return false;
            }

            if (!policy_check(VideoPolicy::class, 'uploadToAlbum', $user, $album->owner)) {
                return false;
            }
        }

        return true;
    }
}
