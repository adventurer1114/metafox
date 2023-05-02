<?php

namespace MetaFox\Photo\Support;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use MetaFox\Photo\Models\Album;
use MetaFox\Photo\Repositories\Eloquent\PhotoRepository;
use MetaFox\Photo\Repositories\PhotoRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;

/**
 * Class Photo.
 */
class Photo
{
    /**
     * @return PhotoRepository|PhotoRepositoryInterface
     */
    public function repository()
    {
        return resolve(PhotoRepositoryInterface::class);
    }

    /**
     * @param User                 $context
     * @param User                 $owner
     * @param array<string, mixed> $attributes  - [array categories, array files, int privacy, array list, string
     *                                          location_name, numeric location_latitude, numeric location_longitude]
     * @param int                  $contextType
     *
     * @return int[]
     * @throws AuthorizationException
     */
    public function createPhoto(User $context, User $owner, array $attributes, int $contextType = Album::TIMELINE_ALBUM): array
    {
        return $this->repository()->createPhoto($context, $owner, $attributes, $contextType);
    }

    public function isVideoAllow(): bool
    {
        return Settings::get('photo.photo_allow_uploading_video_to_photo_album', true);
    }

    public function transformDataForFeed(array $params): array
    {
        $params['content'] = $params['photo_description'] ?? '';

        $params['photo_files'] = $this->handlePhotoFiles($params);

        unset($params['photo_description']);

        unset($params['user_status']);

        return $params;
    }

    /**
     * $data sample
     * [
     *      ... ,
     *      'photo_files' => [
     *          ['id' => 1, 'type' => 'photo', 'status' => 'new'],
     *          ['id' => 2, 'type' => 'photo', 'status' => 'new'],
     *          ['id' => 3, 'type' => 'photo', 'status' => 'remove'],
     *          ['id' => 4, 'type' => 'photo', 'status' => 'remove'],
     *          ...
     *      ],
     * ].
     * @param  array<string, mixed>        $data
     * @return array<string,        mixed>
     */
    protected function handlePhotoFiles(array $data): array
    {
        $photoFiles = [
            'new'    => [],
            'remove' => [],
            'edit'   => [],
        ];

        if (Arr::has($data, 'photo_files')) {
            $files      = collect($data['photo_files'])->groupBy('status')->toArray();
            $photoFiles = array_merge($photoFiles, $files);
        }

        return $photoFiles;
    }
}
