<?php

namespace MetaFox\Layout\Http\Resources\v1\Theme\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Layout\Models\Theme as Model;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item.stub
*/

/**
 * Class ThemeItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @mixin Model
 */
class ThemeItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'theme_id'      => $this->theme_id,
            'title'         => $this->title,
            'resolution'    => $this->resolution,
            'total_variant' => __p('core::web.total_item', ['value' => $this->total_variant]),
            'total_custom'  => __p('core::web.total_item', ['value' => $this->total_variant]),
            'is_active'     => $this->is_system ? null : $this->is_active,
            'is_system'     => $this->is_system,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'links'         => [
                'createVariant' => '/admincp/layout/variant/create?theme_id=' . $this->theme_id,
                'viewVariant'   => sprintf('/admincp/layout/theme/%s/variant/browse', $this->id),
                'viewCustom'    => sprintf('/admincp/layout/theme/%s/snippet/browse', $this->id),
            ],
        ];
    }
}
