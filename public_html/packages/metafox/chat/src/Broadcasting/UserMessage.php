<?php

namespace MetaFox\Chat\Broadcasting;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MetaFox\Chat\Http\Resources\v1\Message\MessageDetail;
use MetaFox\Chat\Models\Message;
use MetaFox\Chat\Repositories\MessageRepositoryInterface;
use MetaFox\Chat\Traits\ReactionTraits;
use MetaFox\Core\Http\Resources\v1\Attachment\AttachmentItemCollection;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityItem;

class UserMessage implements ShouldBroadcast
{
    use SerializesModels;
    use Dispatchable;
    use ReactionTraits;

    private int $userId;
    private Message $message;
    private string $broadcastType;

    public bool $afterCommit = true;

    /**
     * @param Message $message
     * @param int     $userId
     */
    public function __construct(Message $message, int $userId, string $broadcastType)
    {
        $this->message       = $message;
        $this->userId        = $userId;
        $this->broadcastType = $broadcastType;
    }

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
            case Message::MESSAGE_REACT:
            case Message::MESSAGE_UPDATE:
                $type = 'MessageUpdate';
                break;
            default:
                $type = 'UserMessage';
        }

        return $type;
    }

    /**
     * Data to send to client.
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'module_name'   => 'chat',
            'resource_name' => 'message',
            'message_id'    => $this->message->id,
            'message'       => $this->message->message,
            'room_id'       => $this->message->room_id,
            'user_id'       => $this->message->userId(),
            'user'          => new UserEntityItem($this->message->userEntity),
            'type'          => $this->message->type,
            'extra'         => json_decode($this->message->extra),
            'attachments'   => new AttachmentItemCollection($this->message->attachments),
            'reactions'     => $this->normalizeReactions($this->message->reactions),
            'created_at'    => $this->message->created_at,
            'updated_at'    => $this->message->updated_at,
        ];
    }
}
