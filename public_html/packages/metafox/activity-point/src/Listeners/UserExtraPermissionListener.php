<?php

namespace MetaFox\ActivityPoint\Listeners;

use MetaFox\ActivityPoint\Models\PointStatistic;
use MetaFox\ActivityPoint\Policies\StatisticPolicy;
use MetaFox\ActivityPoint\Repositories\PointStatisticRepositoryInterface;
use MetaFox\Platform\Contracts\User;

class UserExtraPermissionListener
{
    /**
     * @param  User                 $context
     * @param  User|null            $user
     * @return array<string, mixed>
     */
    public function handle(User $context, ?User $user = null): array
    {
        $canGiftPoints = $context->hasPermissionTo('activitypoint.can_gift_activity_points');

        return [
            'can_gift_activity_point'         => $user instanceof User && $canGiftPoints,
            'can_view_profile_activity_point' => $this->canViewPointStatistic($context, $user),
        ];
    }

    protected function canViewPointStatistic(User $context, ?User $user = null): bool
    {
        if (!$user instanceof User) {
            return true;
        }

        $statistic = resolve(PointStatisticRepositoryInterface::class)->getModel()
            ->newModelQuery()
            ->where('id', $user->entityId())
            ->first();

        if (!$statistic instanceof PointStatistic) {
            return false;
        }

        return policy_check(StatisticPolicy::class, 'view', $context, $statistic);
    }
}
