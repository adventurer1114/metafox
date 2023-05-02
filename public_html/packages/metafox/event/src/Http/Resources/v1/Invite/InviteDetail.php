<?php

namespace MetaFox\Event\Http\Resources\v1\Invite;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Event\Models\Invite as Model;
use MetaFox\User\Http\Resources\v1\User\UserDetail;

/**
 * Class InviteDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class InviteDetail extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request              $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'event',
            'resource_name' => $this->resource->entityType(),
            'status_id'     => $this->resource->status_id,
            'event_id'      => $this->resource->event_id,
            'user'          => new UserDetail($this->resource->user),
            'owner'         => new UserDetail($this->resource->owner),
        ];
    }
}
