<?php

namespace MetaFox\Photo\Http\Resources\v1\Photo;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Photo\Http\Resources\v1\Album\AlbumDetail;
use MetaFox\Photo\Http\Resources\v1\Category\CategoryItemCollection;
use MetaFox\Photo\Models\Photo as Model;
use MetaFox\Photo\Repositories\PhotoRepositoryInterface;
use MetaFox\Photo\Support\Facades\Album as AlbumContract;
use MetaFox\Photo\Support\Traits\PhotoHasExtra;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\Traits\Helpers\IsFriendTrait;
use MetaFox\Platform\Traits\Helpers\IsLikedTrait;
use MetaFox\Platform\Traits\Helpers\UserReactedTrait;
use MetaFox\Platform\Traits\Http\Resources\HasFeedParam;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/**
 * Class PhotoDetail.
 * @property Model $resource
 */
class  PhotoDetail extends JsonResource
{
    use PhotoHasExtra;
    use HasStatistic;
    use HasFeedParam;
    use IsLikedTrait;
    use IsFriendTrait;
    use UserReactedTrait;

    /**
     * @return array<string, mixed>
     */
    public function getStatistic(): array
    {
        $reactItem = $this->resource->reactItem();

        return [
            'total_like'     => $reactItem instanceof HasTotalLike ? $reactItem->total_like : 0,
            'total_view'     => $this->resource->total_view,
            'total_share'    => $this->resource->total_share,
            'total_comment'  => $reactItem instanceof HasTotalComment ? $reactItem->total_comment : 0,
            'total_reply'    => $reactItem instanceof HasTotalCommentWithReply ? $reactItem->total_reply : 0,
            'total_tag'      => $this->resource->total_tag,
            'total_download' => $this->resource->total_download,
            'total_vote'     => $this->resource->total_vote,
            'total_rating'   => $this->resource->total_rating,
        ];
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        $context = user();

        $repository = resolve(PhotoRepositoryInterface::class);

        $content = null;

        $text = $shortDescription = null;

        $reactItem = $this->resource->reactItem();

        if (null !== $this->resource->content) {
            $content = $this->resource->content;
        }

        if ($this->resource->group_id > 0 && null === $content) {
            $content = $reactItem->content;
        }

        if (null !== $content) {
            $shortDescription = parse_output()->getDescription($content);
            $text             = parse_output()->parse($content);
        }

        $taggedFriends = $repository->getTaggedFriends($context, $this->resource->entityId());

        $fileItem = $this->resource->fileItem;

        $album = null;

        if ($this->resource->album_id > 0) {
            $isDefaultAlbum = false;

            if (null !== $this->resource->album) {
                $isDefaultAlbum = AlbumContract::isDefaultAlbum($this->resource->album->album_type);
            }

            if (!$isDefaultAlbum) {
                $album = new AlbumDetail($this->resource->album);
            }
        }

        return [
            'id'               => $this->resource->entityId(),
            'module_name'      => $this->resource->entityType(),
            'resource_name'    => $this->resource->entityType(),
            'title'            => $this->resource->title,
            'like_type_id'     => $reactItem->entityType(),
            'like_item_id'     => $reactItem->entityId(),
            'comment_type_id'  => $reactItem->entityType(),
            'comment_item_id'  => $reactItem->entityId(),
            'description'      => $shortDescription,
            'text'             => $text,
            'privacy'          => $this->resource->privacy,
            'module_id'        => $this->resource->ownerId() != $this->resource->userId() ? $this->resource->ownerType() : $this->resource->entityType(),
            'item_id'          => $this->resource->ownerId() != $this->resource->userId() ? $this->resource->ownerId() : 0,
            'group_id'         => $this->resource->group_id,
            'album_id'         => $this->resource->album_id,
            'type_id'          => $this->resource->type_id,
            'user'             => new UserEntityDetail($this->resource->userEntity),
            'owner'            => new UserEntityDetail($this->resource->ownerEntity),
            'owner_type_name'  => __p("{$this->resource->ownerType()}::phrase.{$this->resource->ownerType()}"),
            'is_approved'      => $this->resource->is_approved,
            'is_sponsor'       => $this->resource->is_sponsor,
            'is_featured'      => $this->resource->is_featured,
            'is_cover'         => $this->resource->is_cover,
            'is_profile_photo' => $this->resource->is_profile_photo,
            'is_cover_photo'   => $this->resource->is_cover_photo,
            'is_temp'          => $this->resource->is_temp,
            'is_friend'        => $this->isFriend($context, $this->resource->user),
            'is_liked'         => $this->isLike($context, $reactItem),
            'is_pending'       => !$this->resource->is_approved,
            'is_saved'         => PolicyGate::check(
                $this->resource->entityType(),
                'isSavedItem',
                [$context, $this->resource]
            ),
            'width'             => $fileItem->width ?? 0,
            'height'            => $fileItem?->height ?? 0,
            'file_size'         => $fileItem?->file_size ?? 0,
            'mature'            => $this->resource->mature,
            'categories'        => new CategoryItemCollection($this->resource->activeCategories),
            'photo_tags'        => [],
            'tagged_friends'    => new PhotoTaggedFriendCollection($taggedFriends),
            'is_sponsored_feed' => $this->resource->sponsor_in_feed,
            'image'             => $this->resource->images,
            'album'             => $album,
            'creation_date'     => $this->resource->created_at,
            'modification_date' => $this->resource->updated_at,
            'link'              => $this->resource->toLink(),
            'url'               => $this->resource->toUrl(),
            'statistic'         => $this->getStatistic(),
            'extra'             => $this->getCustomExtra(),
            'feed_param'        => $this->getFeedParams(),
        ];
    }
}
