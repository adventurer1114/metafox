<?php

namespace MetaFox\Layout\Http\Resources\v1\Variant\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Layout\Models\Variant as Model;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item.stub
*/

/**
 * class VariantItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @mixin Model
 */
class VariantItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $theme = $this->theme;

        return [
            'id'          => $this->id,
            'theme_id'    => $this->theme_id,
            'theme_title' => $theme?->title ?? $this->theme_id,
            'variant_id'   => $this->variant_id,
            'title'       => $this->title,
            'is_active'   => $this->is_active,
            'is_system'   => $this->is_system,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
            'links'       => [
                'createVariant' => '/admincp/layout/variant/create?theme_id=' . $this->theme_id,
                'viewTheme'    => '/admincp/layout/theme/browse',
            ],
        ];
    }
}
