<?php

namespace MetaFox\Event\Listeners;

use Carbon\Carbon;
use MetaFox\Event\Models\Event;
use MetaFox\Event\Repositories\EventRepositoryInterface;

class CollectTotalItemsStatListener
{
    /**
     * @param  Carbon|null            $after
     * @param  Carbon|null            $before
     * @return array<int, mixed>|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handle(?Carbon $after = null, ?Carbon $before = null): ?array
    {
        return [
            [
                'name'  => Event::ENTITY_TYPE,
                'label' => 'event::phrase.event_stat_label',
                'value' => resolve(EventRepositoryInterface::class)->getTotalItemByPeriod($after, $before),
            ],
        ];
    }
}
