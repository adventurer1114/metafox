<?php

namespace MetaFox\Quiz\Listeners;

use Carbon\Carbon;
use MetaFox\Quiz\Models\Quiz;
use MetaFox\Quiz\Repositories\QuizRepositoryInterface;

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
                'name'  => Quiz::ENTITY_TYPE,
                'label' => 'quiz::phrase.quiz_stat_label',
                'value' => resolve(QuizRepositoryInterface::class)->getTotalItemByPeriod($after, $before),
            ],
        ];
    }
}
