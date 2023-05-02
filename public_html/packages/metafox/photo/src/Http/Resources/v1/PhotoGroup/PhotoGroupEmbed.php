<?php

namespace MetaFox\Photo\Http\Resources\v1\PhotoGroup;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Photo\Models\PhotoGroup as Model;
use MetaFox\Photo\Models\PhotoGroupItem;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/*
|--------------------------------------------------------------------------
| Resource Embed
|--------------------------------------------------------------------------
|
| Resource embed is used when you want attach this resource as embed content of
| activity feed, notification, ....
| @link https://laravel.com/docs/8.x/eloquent-resources#concept-overview
| @link /app/Console/Commands/stubs/module/resources/detail.stub
*/

/**
 * Class PhotoGroupEmbed.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PhotoGroupEmbed extends JsonResource
{
    use HasStatistic;

    public const DEFAULT_LOADED = 4;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $isLoadForEdit = $request->get('embed_object_for_edit', false);

        $limit = self::DEFAULT_LOADED;

        $fetchedPhoto = 0;

        $total = $this->resource->total_item;

        if (!$this->resource->relationLoaded('items')) {
            $this->resource->loadMissing([
                'items' => function (HasMany $q) use ($limit, $isLoadForEdit) {
                    if ($isLoadForEdit === false) {
                        $q->limit($limit);
                    }
                },
            ]);
        }

        $media = [];

        $items = $this->resource->items;

        $isApproved = $this->resource->isApproved();

        if (!$isApproved) {
            $total = $items->count();
        }

        if ($items->count() > 0) {
            if ($isApproved) {
                $items = $items->filter(function (PhotoGroupItem $item) {
                    $detail = $item->detail;

                    if (!$detail instanceof HasApprove) {
                        return true;
                    }

                    return $detail->isApproved();
                });

                $items = collect($items->values());
            }

            $media = $items->map(function (PhotoGroupItem $item) {
                return ResourceGate::asEmbed($item->detail);
            });
        }

        // $fetchedPhoto = $offset === 0 ? self::DEFAULT_LOADED : self::DEFAULT_LOADED + $offset;
        $remain = ($fetchedPhoto >= $total) ? 0 : $total - $fetchedPhoto;

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => $this->resource->moduleName(),
            'resource_name' => $this->resource->entityType(),
            'remain_photo'  => $remain,
            'user_id'       => $this->resource->userId(),
            'user_type'     => $this->resource->userType(),
            'user'          => new UserEntityDetail($this->resource->userEntity),
            'owner_id'      => $this->resource->ownerId(),
            'owner_type'    => $this->resource->ownerType(),
            'photos'        => $media,
            'is_featured'   => $this->resource->is_featured,
            'is_sponsor'    => $this->resource->is_sponsor,
            'is_approved'   => $this->resource->isApproved(),
            'album_id'      => $this->resource->album_id,
            'album'         => ResourceGate::asResource($this->resource->album, 'detail'),
            'statistic'     => $this->getStatistic(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getStatistic(): array
    {
        $default = [
            'total_like'    => $this->resource->total_like,
            'total_comment' => $this->resource->total_comment,
            'total_view'    => $this->resource->total_view,
            'total_photo'   => 0,
            'total_item'    => $this->resource->total_item,
            'total_video'   => 0,
        ];

        return array_merge($default, $this->resource->statistic?->toAggregateData() ?? []);
    }
}
