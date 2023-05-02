<?php

namespace MetaFox\Video\Http\Resources\v1\VideoService\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Video\Models\VideoService as Model;

/**
 * Class VideoServiceItem.
 * @property Model $resource
 */
class VideoServiceItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->resource->entityId(),
            'is_active'     => $this->resource->is_active,
            'module_name'   => 'video',
            'resource_name' => $this->resource->entityType(),
            'name'          => $this->resource->name,
            'driver'        => $this->resource->service_class,
            'detail_link'   => $this->resource->detail_link,
        ];
    }
}
