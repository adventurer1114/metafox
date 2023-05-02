<?php

namespace MetaFox\Photo\Http\Resources\v1\Album;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Photo\Models\Album as Model;
use MetaFox\Photo\Models\AlbumItem;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\Traits\Http\Resources\HasFeedParam;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;

/**
 * Class AlbumEmbed.
 * @property Model $resource
 */
class AlbumEmbed extends JsonResource
{
    use HasFeedParam;

    public const DEFAULT_LOADED = 4;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $limit = self::DEFAULT_LOADED;

        if (!$this->resource->relationLoaded('items')) {
            $this->resource->loadMissing([
                'items' => function (HasMany $query) use ($limit) {
                    $query->limit($limit);
                },
            ]);
        }

        $albumItems = [];

        $items = $this->resource->items;

        if ($items->count() > 0) {
            $albumItems = $this->resource->items->map(function (AlbumItem $item) {
                return ResourceGate::asEmbed($item->detail);
            });
        }

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'photo',
            'resource_name' => $this->resource->entityType(),
            'privacy'       => $this->resource->privacy,
            'items'         => $albumItems,
            'user'          => new UserEntityDetail($this->resource->userEntity),
            'total_item'    => $this->resource->total_item,
            'statistic'     => $this->getStatistic(),
            'feed_id'       => $this->getFeedParams()->resource->entityId(),
            'is_featured'   => $this->resource->is_featured,
            'is_sponsor'    => $this->resource->is_sponsor,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getStatistic(): array
    {
        $reactItem = $this->resource->reactItem();

        return [
            'total_photo'   => $this->resource->total_photo,
            'total_item'    => $this->resource->total_item,
            'total_like'    => $reactItem instanceof HasTotalLike ? $reactItem->total_like : 0,
            'total_share'   => $this->resource->total_share,
            'total_comment' => $reactItem instanceof HasTotalComment ? $reactItem->total_comment : 0,
            'total_reply'   => $reactItem instanceof HasTotalCommentWithReply ? $reactItem->total_reply : 0,
        ];
    }
}
