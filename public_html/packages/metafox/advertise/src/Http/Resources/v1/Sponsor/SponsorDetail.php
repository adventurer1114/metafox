<?php

namespace MetaFox\Advertise\Http\Resources\v1\Sponsor;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Advertise\Models\Sponsor as Model;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/detail.stub
*/

/**
 * Class SponsorDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @mixin Model
 */
class SponsorDetail extends JsonResource
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
            'id'            => $this->id,
            'module_name'   => '',
            'resource_name' => $this->entityType(),
        ];
    }
}
