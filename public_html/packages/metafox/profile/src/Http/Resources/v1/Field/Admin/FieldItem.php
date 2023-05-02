<?php

namespace MetaFox\Profile\Http\Resources\v1\Field\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Profile\Models\Field as Model;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item.stub
*/

/**
 * class FieldItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @mixin Model
 */
class FieldItem extends JsonResource
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
            'id'              => $this->id,
            'resource_name'   => $this->resource->entityType(),
            'field_name'      => $this->field_name,
            'type_id'         => $this->type_id,
            'is_active'       => $this->is_active,
            'section_id'      => $this->section_id,
            'group'           => $this->resource->section?->label,
            'description'     => $this->editingDescription,
            'var_type'        => $this->var_type,
            'view_type'       => $this->view_type,
            'edit_type'       => $this->edit_type,
            'is_register'     => $this->is_register,
            'is_required'     => $this->is_required,
            'ordering'        => $this->ordering,
            'is_search'       => $this->is_search,
            'is_feed'         => $this->is_feed,
            'label'           => $this->resource->editingLabel,
            'has_label'       => $this->resource->has_label,
            'has_description' => $this->resource->has_description,
            'extra'           => $this->extra,
            'links'           => [
                'editItem' => '/admincp/profile/field/edit/' . $this->id,
            ],
        ];
    }
}
