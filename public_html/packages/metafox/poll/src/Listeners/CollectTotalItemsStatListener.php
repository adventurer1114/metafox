<?php

namespace MetaFox\Poll\Listeners;

use Carbon\Carbon;
use MetaFox\Poll\Models\Poll;
use MetaFox\Poll\Repositories\PollRepositoryInterface;

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
                'name'  => Poll::ENTITY_TYPE,
                'label' => 'poll::phrase.poll_stat_label',
                'value' => resolve(PollRepositoryInterface::class)->getTotalItemByPeriod($after, $before),
            ],
        ];
    }
}
