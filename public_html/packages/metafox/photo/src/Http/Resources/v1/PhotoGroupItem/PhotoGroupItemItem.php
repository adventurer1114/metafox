<?php

namespace MetaFox\Photo\Http\Resources\v1\PhotoGroupItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Photo\Models\PhotoGroupItem;
use MetaFox\Platform\Facades\ResourceGate;

/**
 * Class PhotoGroupItemItem.
 * @property PhotoGroupItem $resource
 */
class PhotoGroupItemItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $resource = ResourceGate::asEmbed($this->resource->detail);

        if (!$resource instanceof JsonResource) {
            return [];
        }

        return $resource->toArray($request);
    }
}
