<?php

namespace MetaFox\Chat\Http\Resources\v1\Message;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Chat\Traits\ReactionTraits;
use MetaFox\Core\Http\Resources\v1\Attachment\AttachmentItemCollection;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

class MessageItem extends JsonResource
{
    use ReactionTraits;

    public function toArray($request)
    {
        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'chat',
            'resource_name' => 'message',
            'type'          => $this->resource->type,
            'room_id'       => $this->resource->room_id,
            'message'       => $this->resource->message,
            'user_id'       => $this->resource->user_id,
            'user_type'     => $this->resource->user_type,
            'user'          => new UserEntityDetail($this->resource->userEntity),
            'attachments'   => new MessageAttachmentCollection($this->resource->attachments),
            'extra'         => json_decode($this->resource->extra),
            'reactions'     => $this->normalizeReactions($this->resource->reactions),
            'created_at'    => $this->resource->created_at,
            'updated_at'    => $this->resource->updated_at,
        ];
    }
}
