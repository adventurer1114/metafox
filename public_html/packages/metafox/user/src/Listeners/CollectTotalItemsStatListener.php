<?php

namespace MetaFox\User\Listeners;

use Carbon\Carbon;
use MetaFox\User\Models\User;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;

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
        if ($after) {
            return [
                [
                    'name'  => User::ENTITY_TYPE,
                    'label' => 'user::phrase.user_stat_label',
                    'value' => resolve(UserRepositoryInterface::class)->getTotalItemByPeriod($after, $before),
                ],
            ];
        }

        return [
            [
                'name'  => User::ENTITY_TYPE,
                'label' => 'user::phrase.user_stat_label',
                'value' => resolve(UserRepositoryInterface::class)->getTotalItemByPeriod(),
            ],
            [
                'name'  => 'online_user',
                'label' => 'user::phrase.online_user_stat_label',
                'value' => resolve(UserRepositoryInterface::class)->getOnlineUserCount(),
            ],
            [
                'name'  => 'pending_user',
                'label' => 'user::phrase.pending_user_stat_label',
                'value' => resolve(UserRepositoryInterface::class)->getPendingUserCount(),
            ],
        ];
    }
}
