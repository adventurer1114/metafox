<?php

namespace MetaFox\Chat\Repositories\Eloquent;

use MetaFox\User\Models\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Chat\Repositories\SubscriptionRepositoryInterface;
use MetaFox\Chat\Models\Subscription;
use MetaFox\Platform\Contracts\User as UserContracts;
use MetaFox\User\Support\Facades\UserEntity;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class SubscriptionRepository.
 */
class SubscriptionRepository extends AbstractRepository implements SubscriptionRepositoryInterface
{
    public function model(): string
    {
        return Subscription::class;
    }

    public function createMemberSubscription(UserContracts $context, int $roomId, int $memberId): Subscription
    {
        $user = User::query()->find($memberId);

        $subscription = new Subscription([
            'room_id'      => $roomId,
            'name'         => '',
            'is_favourite' => 0,
            'is_showed'    => $memberId == $context->entityId() ? 1 : 0,
            'user_id'      => $user->id,
            'user_type'    => 'user',
        ]);

        $subscription->save();
        $subscription->refresh();

        return $subscription;
    }

    public function massCreateMemberSubscription(UserContracts $context, int $roomId, array $memberIds): void
    {
        foreach ($memberIds as $memberId) {
            $this->createMemberSubscription($context, $roomId, $memberId);
        }

        foreach ($memberIds as $memberId) {
            $subscriptionName = $this->getSubscriptionName($roomId, $memberId);
            $this->getModel()->query()
                ->where('user_id', '=', $memberId)
                ->where('room_id', '=', $roomId)
                ->update(['name' => $subscriptionName]);
        }
    }

    public function getSubscriptions(int $roomId, bool $ignoreUser = false, int $userId = 0)
    {
        $query = $this->getModel()->newQuery();

        $query = $query->where('room_id', '=', $roomId);

        if ($ignoreUser && $userId) {
            $query = $query->where('user_id', '!=', $userId);
        }

        if (!$ignoreUser && $userId) {
            $query = $query->where('user_id', '=', $userId);
        }

        $subscriptions = $query->get();

        return $subscriptions;
    }

    public function markRead(UserContracts $context, int $roomId)
    {
        return $this->getModel()->newQuery()
            ->where('user_id', '=', $context->entityId())
            ->where('room_id', '=', $roomId)
            ->update(['total_unseen' => 0]);
    }

    public function markAllRead(UserContracts $context, array $attributes)
    {
        $roomIds = $attributes['room_ids'];

        $query = $this->getModel()->newQuery();

        if (!empty($roomIds)) {
            $query = $query->whereIn('room_id', $roomIds);
        }

        return $query
            ->where('user_id', '=', $context->entityId())
            ->where('total_unseen', '!=', 0)
            ->update(['total_unseen' => 0]);
    }

    public function deleteUserSubscriptions(int $userId)
    {
        return $this->getModel()->newQuery()
            ->where('user_id', '=', $userId)
            ->delete();
    }

    protected function getSubscriptionName(int $roomId, int $memberId)
    {
        $name          = '';
        $subscriptions = resolve(SubscriptionRepositoryInterface::class)->getSubscriptions($roomId, true, $memberId);
        if (count($subscriptions) == 1) {
            $subscription = $subscriptions[0];
            $user         = UserEntity::getById($subscription->user_id)->detail;
            $name         = $user->full_name;
        }

        return $name;
    }
}
