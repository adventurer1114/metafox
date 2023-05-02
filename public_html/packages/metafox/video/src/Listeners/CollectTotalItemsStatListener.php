<?php

namespace MetaFox\Video\Listeners;

use Carbon\Carbon;
use MetaFox\Video\Models\Video;
use MetaFox\Video\Repositories\VideoRepositoryInterface;

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
                'name'  => Video::ENTITY_TYPE,
                'label' => 'video::phrase.video_stat_label',
                'value' => resolve(VideoRepositoryInterface::class)->getTotalItemByPeriod($after, $before),
            ],
        ];
    }
}
