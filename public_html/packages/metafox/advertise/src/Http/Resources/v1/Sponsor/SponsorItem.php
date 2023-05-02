<?php

namespace MetaFox\Advertise\Http\Resources\v1\Sponsor;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Advertise\Models\Sponsor as Model;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item.stub
*/

/**
 * Class SponsorItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @mixin Model
 */
class SponsorItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
        ];
    }
}
