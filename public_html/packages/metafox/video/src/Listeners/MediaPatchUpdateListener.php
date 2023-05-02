<?php

namespace MetaFox\Video\Listeners;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Media;
use MetaFox\Platform\Contracts\TempFileModel;
use MetaFox\Platform\Contracts\User;
use MetaFox\Video\Models\Video;
use MetaFox\Video\Repositories\VideoRepositoryInterface;

class MediaPatchUpdateListener
{
    protected VideoRepositoryInterface $repository;

    public function __construct(VideoRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Photo set feed content won't apply to every single photo. DO NOT assign content to per photo.
     *
     * @param  User                   $user
     * @param  User                   $owner
     * @param  TempFileModel          $file
     * @param  array<string, mixed>   $params
     * @param  Media                  $media
     * @return Content|null
     * @throws AuthorizationException
     * @throws Exception
     */
    public function handle(User $user, string $itemType, int $itemId, array $attributes): ?bool
    {
        if (Video::ENTITY_TYPE != $itemType) {
            return null;
        }

        $this->repository->updatePatchVideo($itemId, $attributes);

        return true;
    }
}
