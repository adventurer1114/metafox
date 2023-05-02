<?php

namespace MetaFox\Page\Listeners;

use Carbon\Carbon;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Repositories\PageRepositoryInterface;

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
                'name'  => Page::ENTITY_TYPE,
                'label' => 'page::phrase.page_stat_label',
                'value' => resolve(PageRepositoryInterface::class)->getTotalItemByPeriod($after, $before),
            ],
        ];
    }
}
