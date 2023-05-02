<?php

namespace MetaFox\Photo\Http\Resources\v1\PhotoGroup;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Photo\Models\PhotoGroup as Model;
use MetaFox\Photo\Models\PhotoGroupItem;
use MetaFox\Photo\Support\Browse\Traits\PhotoGroup\ExtraTrait;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\Traits\Helpers\IsLikedTrait;
use MetaFox\Platform\Traits\Http\Resources\HasFeedParam;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/**
 * |--------------------------------------------------------------------------
 * | Resource Detail
 * |--------------------------------------------------------------------------
 * | stub: /packages/resources/detail.stub
 * | @link https://laravel.com/docs/8.x/eloquent-resources#concept-overview.
 **/

/**
 * Class PhotoGroupDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PhotoGroupDetail extends JsonResource
{
    use HasStatistic;
    use HasFeedParam;
    use IsLikedTrait;
    use ExtraTrait;

    public const DEFAULT_LOADED = 4;

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

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string,           mixed>
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        $context = user();

        $limit        = self::DEFAULT_LOADED;

        $fetchedPhoto = 0;

        $total = $this->resource->total_item;

        if (!$this->resource->relationLoaded('items')) {
            $this->resource->loadMissing([
                'items' => function (HasMany $q) use ($limit) {
                    // $offset = $request->get('offset', 0);
                    // $q->offset($offset);
                    $q->limit($limit);
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
            'total_item'    => $total,
            'remain_photo'  => $remain,
            'album_id'      => $this->resource->album_id,
            'description'   => $this->resource->content,
            'is_liked'      => $this->isLike($context, $this->resource),
            'user'          => new UserEntityDetail($this->resource->userEntity),
            'owner'         => new UserEntityDetail($this->resource->ownerEntity),
            'photos'        => $media,
            'link'          => $this->resource->toLink(),
            'url'           => $this->resource->toUrl(),
            'statistic'     => $this->getStatistic(),
            'extra'         => $this->getExtra(),
            'feed_param'    => $this->getFeedParams(),
        ];
    }
}
