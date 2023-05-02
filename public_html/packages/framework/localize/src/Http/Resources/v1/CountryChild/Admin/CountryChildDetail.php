<?php

namespace MetaFox\Localize\Http\Resources\v1\CountryChild\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Localize\Models\CountryChild as Model;

/**
 * Class CountryDetail.
 * @property Model $resource
 */
class CountryChildDetail extends JsonResource
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
            'module_name'   => 'core',
            'resource_name' => $this->resource->entityType(),
            'country_iso'   => $this->resource->country_iso,
            'name'          => $this->resource->name,
            'ordering'      => $this->resource->ordering,
        ];
    }
}
