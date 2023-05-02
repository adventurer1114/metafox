<?php

namespace MetaFox\User\Http\Resources\v1\CancelFeedback\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\User\Models\CancelFeedback as Model;

/**
 * Class CancelFeedbackItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CancelFeedbackItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request              $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $reason = $this->resource->reason;

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'user',
            'resource_name' => $this->resource->entityType(),
            'name'          => $this->resource->name,
            'email'         => $this->resource->email ?? 'none',
            'phone_number'  => $this->resource->phone_number ?? 'none',
            'reason_text'   => $reason->title,
            'feedback_text' => $this->resource->feedback_text,
        ];
    }
}
