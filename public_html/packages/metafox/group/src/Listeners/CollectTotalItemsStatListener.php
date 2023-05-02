<?php

namespace MetaFox\Group\Listeners;

use Carbon\Carbon;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Repositories\GroupRepositoryInterface;

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
                'name'  => Group::ENTITY_TYPE,
                'label' => 'group::phrase.group_stat_label',
                'value' => resolve(GroupRepositoryInterface::class)->getTotalItemByPeriod($after, $before),
            ],
        ];
    }
}
