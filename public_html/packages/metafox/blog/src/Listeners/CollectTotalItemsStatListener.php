<?php

namespace MetaFox\Blog\Listeners;

use Carbon\Carbon;
use MetaFox\Blog\Models\Blog;
use MetaFox\Blog\Repositories\BlogRepositoryInterface;

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
                'name'  => Blog::ENTITY_TYPE,
                'label' => 'blog::phrase.blog_stat_label',
                'value' => resolve(BlogRepositoryInterface::class)->getTotalItemByPeriod($after, $before),
            ],
        ];
    }
}
