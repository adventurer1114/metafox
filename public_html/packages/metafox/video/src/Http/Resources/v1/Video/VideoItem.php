<?php

namespace MetaFox\Video\Http\Resources\v1\Video;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\Traits\Helpers\IsLikedTrait;
use MetaFox\Platform\Traits\Http\Resources\HasExtra;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\Video\Http\Resources\v1\Category\CategoryItemCollection;
use MetaFox\Video\Models\Video;
use MetaFox\Video\Support\Browse\Traits\Video\HandleContentTrait;

/**
 * Class VideoItem.
 *
 * @property Video $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class VideoItem extends JsonResource
{
    use HasStatistic;
    use HasExtra;
    use IsLikedTrait;
    use HandleContentTrait;

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

        $content = match ($this->resource->group_id > 0) {
            true  => $this->handleContentForUpload(),
            false => $this->handleContentForLink(),
        };

        if ($this->resource->group_id == 0) {
            $content = parse_output()->getDescription($content);
        }

        return [
            'id'                => $this->resource->entityId(),
            'album_id'          => $this->resource->album_id,
            'album'             => ResourceGate::asResource($this->resource->album, 'detail'),
            'module_name'       => $this->resource->moduleName(),
            'resource_name'     => $this->resource->entityType(),
            'title'             => $this->resource->title,
            'description'       => $content,
            'is_featured'       => $this->resource->is_featured,
            'is_sponsor'        => $this->resource->is_sponsor,
            'is_sponsored_feed' => $this->resource->sponsor_in_feed,
            'is_processing'     => $this->resource->is_processing,
            'privacy'           => $this->resource->privacy,
            'is_liked'          => $this->isLike($context),
            'is_pending'        => !$this->resource->is_approved,
            'is_saved'          => PolicyGate::check(
                $this->resource->entityType(),
                'isSavedItem',
                [$context, $this->resource]
            ),
            'module_id'     => $this->resource->ownerId() != $this->resource->userId() ? $this->resource->ownerType() : $this->resource->entityType(),
            'item_id'       => $this->resource->ownerId() != $this->resource->userId() ? $this->resource->ownerId() : 0,
            'destination'   => $this->resource->destination,
            'duration'      => $this->resource->duration,
            'video_url'     => $this->resource->video_url,
            'embed_code'    => $this->resource->embed_code,
            'image'         => $this->resource->images,
            'statistic'     => $this->getStatistic(),
            'link'          => $this->resource->toLink(),
            'url'           => $this->resource->toUrl(),
            'user'          => new UserEntityDetail($this->resource->userEntity),
            'categories'    => new CategoryItemCollection($this->resource->categories),
            'creation_date' => $this->resource->created_at,
            'extra'         => $this->getExtra(),
            'is_creator'    => $context->entityId() == $this->resource->userId(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getStatistic(): array
    {
        $reactItem = $this->resource->reactItem();

        return [
            'total_like'    => $reactItem instanceof HasTotalLike ? $reactItem->total_like : 0,
            'total_comment' => $reactItem instanceof HasTotalComment ? $reactItem->total_comment : 0,
            'total_share'   => $this->resource->total_share,
            'total_view'    => $this->resource->total_view,
            'total_reply'   => $reactItem instanceof HasTotalCommentWithReply ? $reactItem->total_reply : 0,
            'total_rating'  => $this->resource->total_rating,
            'total_score'   => $this->resource->total_score,
        ];
    }
}
