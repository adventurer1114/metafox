<?php

namespace MetaFox\Chat\Http\Resources\v1\Message;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Core\Http\Resources\v1\Attachment\AttachmentItemCollection;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

class ReplyMessageDetail extends JsonResource
{
    public function toArray($request):array
    {
        return [
            'id'                => $this->resource->entityId(),
            'type'              => $this->resource->type,
            'message'           => $this->resource->message,
            'room_id'           => $this->resource->room_id,
            'user_id'           => $this->resource->user_id,
            'user_type'         => $this->resource->user_type,
            'user'              => new UserEntityDetail($this->resource->userEntity),
            'extra'             => json_decode($this->resource->extra),
            'attachments'       => new AttachmentItemCollection($this->resource->attachments),
            'created_at'        => $this->resource->created_at,
            'updated_at'        => $this->resource->updated_at,
        ];
    }
}
