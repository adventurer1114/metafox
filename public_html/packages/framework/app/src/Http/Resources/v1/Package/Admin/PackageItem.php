<?php

namespace MetaFox\App\Http\Resources\v1\Package\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\App\Models\Package as Model;

/**
 * Class PackageItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PackageItem extends JsonResource
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
        $obj       = $this->resource;
        $expiredAt = $obj->expired_at;

        return [
            'id'             => $obj->id,
            'name'           => $obj->name,
            'title'          => $obj->title,
            'version'        => $obj->version,
            'latest_version' => $obj->latest_version ?? $obj->version,
            'is_active'      => $obj->is_core ? true : $obj->is_active,
            'is_installed'   => $obj->is_installed,
            'is_purchased'   => $obj->is_purchased,
            'purchased_at'   => $obj->purchased_at,
            'is_core'        => $obj->is_core,
            'author'         => [
                'name' => $obj->author,
                'url'  => $obj->author_url,
            ],
            'internal_url'       => $obj->internal_url,
            'expired_at'         => $expiredAt,
            'is_expired'         => $expiredAt && Carbon::parse($expiredAt)->lt(Carbon::now()),
            'internal_admin_url' => $obj->is_active ? $obj->internal_admin_url : '',
            'type'               => $obj->type,
        ];
    }
}
