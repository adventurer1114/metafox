<?php

namespace MetaFox\Forum\Listeners;

use Carbon\Carbon;
use MetaFox\Forum\Models\ForumPost;
use MetaFox\Forum\Models\ForumThread;
use MetaFox\Forum\Repositories\ForumPostRepositoryInterface;
use MetaFox\Forum\Repositories\ForumThreadRepositoryInterface;

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
                'name'  => ForumPost::ENTITY_TYPE,
                'label' => 'forum::phrase.forum_post_stat_label',
                'value' => resolve(ForumPostRepositoryInterface::class)->getTotalItemByPeriod($after, $before),
            ],
            [
                'name'  => ForumThread::ENTITY_TYPE,
                'label' => 'forum::phrase.forum_thread_stat_label',
                'value' => resolve(ForumThreadRepositoryInterface::class)->getTotalItemByPeriod($after, $before),
            ],
        ];
    }
}
