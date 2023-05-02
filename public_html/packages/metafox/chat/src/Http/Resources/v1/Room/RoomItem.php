<?php

namespace MetaFox\Chat\Http\Resources\v1\Room;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Chat\Models\Room;
use MetaFox\Chat\Traits\RoomInfoTraits;

/**
 * Class RoomItem.
 * @property Room $resource
 */
class RoomItem extends JsonResource
{
    use RoomInfoTraits;

    public function toArray($request)
    {
        $context = user();

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'chat',
            'resource_name' => 'room',
            'uid'           => $this->resource->uid,
            'name'          => $this->resource->name,
            'is_archived'   => $this->resource->is_archived,
            'is_readonly'   => $this->resource->is_readonly,
            'type'          => $this->resource->type,
            'other_members' => $this->getOtherMembers($this->resource),
            'last_message'  => $this->getLastMessage($context->entityId(), $this->resource),
            'total_unseen'  => $this->getTotalUnseen($this->resource),
            'created_at'    => $this->resource->created_at,
            'updated_at'    => $this->resource->updated_at,
        ];
    }
}
