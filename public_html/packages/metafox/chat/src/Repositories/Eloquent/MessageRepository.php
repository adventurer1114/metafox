<?php

namespace MetaFox\Chat\Repositories\Eloquent;

use Carbon\Carbon;
use MetaFox\Chat\Broadcasting\RoomMessage;
use MetaFox\Chat\Http\Resources\v1\Message\ReplyMessageDetail;
use MetaFox\Chat\Jobs\MessageQueueJob;
use MetaFox\Chat\Models\Room;
use MetaFox\Chat\Models\Subscription;
use MetaFox\Chat\Policies\MessagePolicy;
use MetaFox\Chat\Repositories\SubscriptionRepositoryInterface;
use MetaFox\Core\Repositories\AttachmentRepositoryInterface;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Chat\Repositories\MessageRepositoryInterface;
use MetaFox\Chat\Models\Message;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\User\Http\Resources\v1\User\UserSimple;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class MessageRepository.
 */
class MessageRepository extends AbstractRepository implements MessageRepositoryInterface
{
    public function model()
    {
        return Message::class;
    }

    public function viewMessages(array $attributes)
    {
        $limit     = $attributes['limit'];
        $search    = $attributes['q'];
        $lastMsgId = $attributes['last_message_id'];

        $query = $this->getModel()->newQuery();

        if ($search != '') {
            $query = $query->where('chat_messages.type', '<>', 'delete');
            $query = $query->addScope(new SearchScope($search, ['message']));
        }

        if (!empty($lastMsgId)) {
            $query = $query->where('chat_messages.id', '<', $lastMsgId);
        }

        if (isset($attributes['room_id'])) {
            $query = $query->where('room_id', '=', $attributes['room_id']);

            $subscription = Subscription::query()
                ->where('room_id', '=', $attributes['room_id'])
                ->where('user_id', '=', user()->entityId())
                ->first();
            if (!empty($subscription) && $subscription->is_deleted == 0 && !empty($subscription->rejoin_at)) {
                $query = $query->where('created_at', '>=', $subscription->rejoin_at);
            }
        }

        $messageData = $query->orderByDesc('created_at')
            ->simplePaginate($limit, ['chat_messages.*']);

        return $messageData;
    }

    public function viewMessage(User $context, int $id)
    {
        $message = $this
            ->with(['attachments'])
            ->find($id);

        return $message;
    }

    public function addMessage(User $context, array $attributes): Message
    {
        $room = Room::query()->getModel()
            ->with(['subscriptions'])
            ->find($attributes['room_id']);

        policy_authorize(MessagePolicy::class, 'create', $context, $room);

        $replyId = !empty($attributes['reply_id']) ? (int) $attributes['reply_id'] : 0;
        $extra   = null;
        if ($replyId) {
            $message       = $this->getModel()->newQuery()->find($replyId);
            $messageDetail =  new ReplyMessageDetail($message);
            $extra         = $messageDetail->toJson();
        }

        $attributes = array_merge([
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
            'extra'     => $extra,
        ], $attributes);

        $message = new Message($attributes);
        $message->save();

        if (!empty($attributes['attachments'])) {
            resolve(AttachmentRepositoryInterface::class)->updateItemId($attributes['attachments'], $message);
        }

        $message->refresh();

        Subscription::query()->getModel()
            ->where([
                'room_id' => $attributes['room_id'],
            ])
            ->touch('updated_at');

        $subscriptions = resolve(SubscriptionRepositoryInterface::class)->getSubscriptions($attributes['room_id']);
        foreach ($subscriptions as $subscription) {
            $userId = $subscription->user_id;
            if (($subscription->user_id != $context->entityId())) {
                if ($subscription->is_showed == 0) {
                    $subscription->update(['is_showed' => 1]);
                }
                if ($subscription->is_deleted) {
                    $subscription->update(['is_deleted' => 0, 'rejoin_at' => $message->created_at]);
                }
                $subscription->increment('total_unseen');
            }
            broadcast(new RoomMessage($attributes['room_id'], $userId, Room::ROOM_UPDATED));
            MessageQueueJob::dispatch($message, $userId, Message::MESSAGE_CREATE);
        }

        return $message;
    }

    public function getRoomLastMessage(int $userId, int $roomId): Message|null
    {
        $query = $this->getModel()->newQuery();

        $subscription = Subscription::query()
            ->where('user_id', '=', $userId)
            ->where('room_id', '=', $roomId)
            ->first();

        if (!empty($subscription) && $subscription->is_deleted == 0 && !empty($subscription->rejoin_at)) {
            $query = $query->where('created_at', '>=', $subscription->rejoin_at);
        }

        $message = $query
            ->with(['attachments'])
            ->where('room_id', '=', $roomId)
            ->orderByDesc('created_at')
            ->first();

        return $message;
    }

    public function updateMessage(User $context, int $id, array $attributes): Message
    {
        $message = $this->find($id);
        $message->fill($attributes);
        $message->save();
        $message->refresh();

        if (isset($attributes['type']) && $attributes['type'] == 'delete') {
            Subscription::query()->getModel()
                ->where([
                    'room_id' => $message->room_id,
                ])
                ->touch('updated_at');
        }

        $subscriptions = resolve(SubscriptionRepositoryInterface::class)->getSubscriptions($message->room_id);
        foreach ($subscriptions as $subscription) {
            $userId = $subscription->user_id;
            if ($subscription->is_deleted == 0) {
                MessageQueueJob::dispatch($message, $userId, Message::MESSAGE_UPDATE);
                broadcast(new RoomMessage($message->room_id, $userId, Room::ROOM_UPDATED));
            }
        }

        return $message;
    }

    public function reactMessage(User $context, int $id, array $params)
    {
        $message = $this->find($id);

        $reactions = $message->reactions;

        $itemReactions = [];
        if (!empty($reactions)) {
            $itemReactions = json_decode($reactions, true);
        }

        if ($params['remove'] || $params['react'] != '') {
            foreach ($itemReactions as $reactKey => $itemReaction) {
                if (in_array($context->entityId(), $itemReaction)) {
                    $idx = array_search($context->entityId(), $itemReaction);
                    unset($itemReactions[$reactKey][$idx]);
                }
            }
        }

        if ($params['react'] != '') {
            $itemReactions[$params['react']][] = $context->entityId();
        }

        foreach ($itemReactions as $reactKey => $itemReaction) {
            if (count($itemReaction) == 0) {
                unset($itemReactions[$reactKey]);
            }
        }

        $message->fill(['reactions' => count($itemReactions) == 0 ? null : json_encode($itemReactions)]);
        $message->save();
        $message->refresh();

        $subscriptions = resolve(SubscriptionRepositoryInterface::class)->getSubscriptions($message->room_id);
        foreach ($subscriptions as $subscription) {
            $userId = $subscription->user_id;
            if ($subscription->is_deleted == 0) {
                MessageQueueJob::dispatch($message, $userId, Message::MESSAGE_UPDATE);
            }
        }

        return $message;
    }

    public function normalizeReactions(array|null $reactions)
    {
        if ($reactions == null) {
            return null;
        }

        $reactionsDetails = [];
        foreach ($reactions as $key => $reaction) {
            foreach ($reaction as $item) {
                $user                     = resolve(UserRepositoryInterface::class)->find($item);
                $reactionsDetails[$key][] = new UserSimple($user);
            }
        }

        return $reactionsDetails;
    }
}
