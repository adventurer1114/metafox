<?php

namespace MetaFox\Chat\Policies;

use Illuminate\Support\Arr;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Contracts\UserBlockedSupportContract;
use MetaFox\User\Models\User as UserModel;

class MessagePolicy
{
    use HasPolicyTrait;

    public function create(User $user, ?Entity $room): bool
    {
        $subscriptionUserIds = $room?->subscriptions->pluck('user_id')->toArray();
        if (!in_array($user->entityId(), $subscriptionUserIds)) {
            return false;
        }

        $otherUserId = Arr::first(array_diff($subscriptionUserIds, [$user->entityId()]));
        if (empty($otherUserId)) {
            return false;
        }

        $otherUser   = UserModel::query()->getModel()->find($otherUserId);
        $userBlock   = resolve(UserBlockedSupportContract::class);

        return !$userBlock->isBlocked($otherUser, $user);
    }
}
