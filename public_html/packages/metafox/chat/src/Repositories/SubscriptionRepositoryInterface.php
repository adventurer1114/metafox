<?php

namespace MetaFox\Chat\Repositories;

use MetaFox\Chat\Models\Subscription;
use MetaFox\Platform\Contracts\User;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface Subscription.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface SubscriptionRepositoryInterface
{
    public function createMemberSubscription(User $context, int $roomId, int $memberId): Subscription;

    public function massCreateMemberSubscription(User $context, int $roomId, array $memberIds): void;

    public function getSubscriptions(int $roomId, bool $ignoreUser = false, int $userId = 0);

    public function markRead(User $context, int $roomId);

    public function markAllRead(User $context, array $attributes);

    public function deleteUserSubscriptions(int $userId);
}
