<?php

namespace MetaFox\Announcement\Http\Resources\v1\AnnouncementView;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Announcement\Models\AnnouncementView as Model;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/**
 * Class AnnouncementDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class AnnouncementViewDetail extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'              => $this->resource->entityId(),
            'module_name'     => 'announcement',
            'resource_name'   => $this->resource->entityType(),
            'user'            => new UserEntityDetail($this->resource->userEntity),
            'creation_date'   => $this->resource->created_at,
            'moderation_date' => $this->resource->updated_at,
        ];
    }
}
