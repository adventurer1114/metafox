<?php

namespace MetaFox\Music\Http\Resources\v1\Song;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Core\Http\Resources\v1\Attachment\AttachmentItemCollection;
use MetaFox\Hashtag\Traits\HasHashtagTextTrait;
use MetaFox\Music\Http\Resources\v1\Album\AlbumDetail;
use MetaFox\Music\Http\Resources\v1\Genre\GenreItemCollection;
use MetaFox\Music\Models\Song;
use MetaFox\Music\Support\Browse\Traits\Song\ExtraTrait;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\Video\Support\Browse\Traits\Video\HandleContentTrait;

/**
 * Class SongEmbed.
 * @property Song $resource
 */
class SongEmbed extends JsonResource
{
    use HasStatistic;
    use HandleContentTrait;
    use HasHashtagTextTrait;
    use ExtraTrait;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     * @throws AuthenticationException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request): array
    {
        $context = user();

        $album = $this->resource->album ?? null;

        if (null !== $album) {
            $album = new AlbumDetail($album);
        }

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => $this->resource->moduleName(),
            'resource_name' => $this->resource->entityType(),
            'name'          => $this->resource->name,
            'file_name'     => $this->resource->original_name,
            'description'   => $this->resource->description,
            'is_featured'   => $this->resource->is_featured,
            'is_sponsor'    => $this->resource->is_sponsor,
            'privacy'       => $this->resource->privacy,
            'is_saved'      => PolicyGate::check(
                $this->resource->entityType(),
                'isSavedItem',
                [$context, $this->resource]
            ),
            'module_id'         => $this->resource->ownerId() != $this->resource->userId() ? $this->resource->ownerType() : $this->resource->entityType(),
            'item_id'           => $this->resource->ownerId() != $this->resource->userId() ? $this->resource->ownerId() : 0,
            'duration'          => $this->resource->duration,
            'image'             => $this->resource->images,
            'statistic'         => $this->getStatistic(),
            'link'              => $this->resource->toLink(),
            'destination'       => $this->resource->link_media_file,
            'url'               => $this->resource->toUrl(),
            'user'              => new UserEntityDetail($this->resource->userEntity),
            'owner'             => new UserEntityDetail($this->resource->ownerEntity),
            'attachments'       => new AttachmentItemCollection($this->resource->attachments),
            'owner_type_name'   => __p("{$this->resource->ownerType()}::phrase.{$this->resource->ownerType()}"),
            'genres'            => new GenreItemCollection($this->resource->genres),
            'creation_date'     => $this->resource->created_at,
            'view_id'           => $this->resource->view_id,
            'modification_date' => $this->resource->updated_at,
            'album_id'          => $this->resource->album_id,
            'album'             => $album,
            'extra'             => $this->getExtra(),
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
            'total_play'    => $this->resource->total_play,
            'total_reply'   => $reactItem instanceof HasTotalCommentWithReply ? $reactItem->total_reply : 0,
            'total_rating'  => $this->resource->total_rating,
            'total_score'   => $this->resource->total_score,
        ];
    }
}
