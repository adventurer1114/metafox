<?php

namespace MetaFox\Localize\Http\Resources\v1\Currency\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Localize\Models\Currency as Model;

/**
 * Class CurrencyItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @mixin Model
 */
class CurrencyItem extends JsonResource
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
            'id'         => $this->id,
            'code'       => $this->code,
            'symbol'     => $this->symbol,
            'name'       => $this->name,
            'format'     => $this->format,
            'is_active'  => $this->is_default ? null : $this->is_active,
            'is_default' => $this->is_default ? null : false,
            'ordering'   => $this->ordering,
        ];
    }
}
