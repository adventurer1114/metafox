<?php

namespace MetaFox\Chat\Traits;

use MetaFox\Chat\Http\Resources\v1\Message\LastMessageDetail;
use MetaFox\Chat\Models\Room;
use MetaFox\Chat\Repositories\MessageRepositoryInterface;
use MetaFox\Chat\Repositories\SubscriptionRepositoryInterface;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\User\Support\Facades\UserEntity;

trait RoomInfoTraits
{
    public function getOtherMembers(Room $resource): array
    {
        $members       = [];
        $roomId        = $resource->id;
        $context       = user();
        $subscriptions = resolve(SubscriptionRepositoryInterface::class)->getSubscriptions($roomId, true, $context->entityId());

        foreach ($subscriptions as $subscription) {
            $user      = new UserEntityDetail($subscription->userEntity);
            $members[] = $user;
        }

        return $members;
    }

    public function getLastMessage(int $userId, Room $resource)
    {
        $message = resolve(MessageRepositoryInterface::class)->getRoomLastMessage($userId, $resource->id);

        return new LastMessageDetail($message);
    }

    public function getChatRoomName(Room $resource)
    {
        $roomId        = $resource->id;
        $context       = user();
        $subscriptions = resolve(SubscriptionRepositoryInterface::class)->getSubscriptions($roomId, true, $context->entityId());

        if (count($subscriptions) > 1) {
            return '';
        } else {
            $subscription = $subscriptions[0];
            $user         = UserEntity::getById($subscription->user_id)->detail;

            return $user->full_name;
        }
    }

    public function getTotalUnseen(Room $resource)
    {
        $roomId        = $resource->id;
        $context       = user();
        $subscriptions = resolve(SubscriptionRepositoryInterface::class)->getSubscriptions($roomId, false, $context->entityId());

        if (!empty($subscriptions)) {
            $subscription = $subscriptions[0];

            return $subscription->total_unseen;
        }

        return 0;
    }
}
