<?php

namespace MetaFox\Chat\Broadcasting;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MetaFox\Chat\Http\Resources\v1\Message\LastMessageDetail;
use MetaFox\Chat\Models\Message;
use MetaFox\Chat\Models\Room;
use MetaFox\Chat\Repositories\MessageRepositoryInterface;
use MetaFox\Chat\Repositories\SubscriptionRepositoryInterface;
use MetaFox\User\Http\Resources\v1\User\UserSimple;
use MetaFox\User\Support\User;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;

class RoomMessage implements ShouldBroadcast
{
    use SerializesModels;
    use Dispatchable;

    private int $roomId;
    private int $userId;
    private string $broadcastType;

    public bool $afterCommit = true;

    /**
     * @param int    $roomId
     * @param int    $userId
     * @param string $broadcastType
     */
    public function __construct(int $roomId, int $userId, string $broadcastType)
    {
        $this->roomId        = $roomId;
        $this->userId        = $userId;
        $this->broadcastType = $broadcastType;
    }

    /**
     * Authenticate the user's access to the channel.
     * Should check is user is subscribed or room is public.
     *
     * @param  \MetaFox\User\Models\User $user
     * @param  mixed                     $message
     * @return array|bool
     */
    public function join(User $user, Message $message)
    {
        return true;
    }

    /**
     * Channel for client to register.
     * @return string
     */
    public function broadcastOn()
    {
        return 'user.' . $this->userId;
    }

    /**
     * Event name for client to register.
     * @return string
     */
    public function broadcastAs()
    {
        switch ($this->broadcastType) {
            case Room::ROOM_UPDATED:
                $type = 'RoomUpdated';
                break;
            case Room::ROOM_DELETED:
                $type = 'RoomDeleted';
                break;
            default:
                $type = '';
        }

        return $type;
    }

    /**
     * Data to send to client.
     * @return array
     */
    public function broadcastWith()
    {
        switch ($this->broadcastType) {
            case Room::ROOM_UPDATED:
                $subscriptions = resolve(SubscriptionRepositoryInterface::class)->getSubscriptions($this->roomId, false, $this->userId);
                $subscription  = $subscriptions[0];

                $includedUserSubscriptions = resolve(SubscriptionRepositoryInterface::class)->getSubscriptions($this->roomId, true, $this->userId);
                $includedUserSubscription  = $includedUserSubscriptions[0];
                $user                      = UserEntity::getById($includedUserSubscription->user_id)->detail;
                $name                      = $user->full_name;

                $members = [];
                foreach ($includedUserSubscriptions as $includedUserSubscription) {
                    $user      = resolve(UserRepositoryInterface::class)->find($includedUserSubscription->user_id);
                    $user      = new UserSimple($user);
                    $members[] = $user;
                }

                $room          = Room::query()->getModel()->find($this->roomId);
                $res           = [
                    'id'            => $room->entityId(),
                    'module_name'   => 'chat',
                    'resource_name' => 'room',
                    'uid'           => $room->uid,
                    'name'          => $name,
                    'is_archived'   => $room->is_archived,
                    'is_readonly'   => $room->is_readonly,
                    'type'          => $room->type,
                    'other_members' => $members,
                    'last_message'  => $this->getLastMessage($this->userId, $room),
                    'total_unseen'  => $subscription->total_unseen,
                    'created_at'    => $room->created_at,
                    'updated_at'    => $room->updated_at,
                ];

                return $res;
            case Room::ROOM_DELETED:
            default:
                return [
                    'id' => $this->roomId,
                ];
        }
    }

    protected function getLastMessage(int $userId, Room $resource)
    {
        $message = resolve(MessageRepositoryInterface::class)->getRoomLastMessage($userId, $resource->id);

        return new LastMessageDetail($message);
    }
}
