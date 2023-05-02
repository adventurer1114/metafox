<?php

namespace MetaFox\Report\Http\Resources\v1\ReportReason\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Report\Models\ReportReason as Model;

/**
 * Class ReportReasonItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ReportReasonItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->resource->entityId(),
            'name'          => $this->resource->name,
            'ordering'      => $this->resource->ordering,
            'creation_date' => $this->resource->created_at,
        ];
    }
}
