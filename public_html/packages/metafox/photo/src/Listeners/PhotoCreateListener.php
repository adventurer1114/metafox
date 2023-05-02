<?php

namespace MetaFox\Photo\Listeners;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use MetaFox\Photo\Models\Photo;
use MetaFox\Photo\Repositories\PhotoRepositoryInterface;

class PhotoCreateListener
{
    /**
     * @param mixed $data
     *
     * @return Collection
     * @throws AuthorizationException
     */
    public function handle(...$data): Collection
    {
        $service = resolve(PhotoRepositoryInterface::class);

        $photoIds = $service->createPhoto(...$data);
        $photo    = Photo::query()->whereIn('id', $photoIds)->get();

        return collect($photo);
    }
}
