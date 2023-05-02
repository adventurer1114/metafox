<?php

namespace MetaFox\Photo\Listeners;

use Carbon\Carbon;
use MetaFox\Photo\Models\Album;
use MetaFox\Photo\Models\Photo;
use MetaFox\Photo\Repositories\AlbumRepositoryInterface;
use MetaFox\Photo\Repositories\PhotoRepositoryInterface;

class CollectTotalItemsStatListener
{
    /**
     * @param  Carbon|null            $after
     * @param  Carbon|null            $before
     * @return array<int, mixed>|null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handle(?Carbon $after = null, ?Carbon $before = null): ?array
    {
        return [
            [
                'name'  => Album::ENTITY_TYPE,
                'label' => 'photo::phrase.photo_album_stat_label',
                'value' => resolve(AlbumRepositoryInterface::class)->getTotalItemByPeriod($after, $before),
            ],
            [
                'name'  => Photo::ENTITY_TYPE,
                'label' => 'photo::phrase.photo_stat_label',
                'value' => resolve(PhotoRepositoryInterface::class)->getTotalItemByPeriod($after, $before),
            ],
        ];
    }
}
