<?php

namespace MetaFox\Music\Http\Resources\v1\Playlist;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Core\Http\Resources\v1\Attachment\AttachmentItemCollection;
use MetaFox\Hashtag\Traits\HasHashtagTextTrait;
use MetaFox\Music\Models\Playlist;
use MetaFox\Music\Support\Browse\Traits\Playlist\StatisticTrait;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\Video\Support\Browse\Traits\Video\HandleContentTrait;

/**
 * Class PlaylistEmbed.
 * @property Playlist $resource
 */
class PlaylistEmbed extends JsonResource
{
    use StatisticTrait;
    use HandleContentTrait;
    use HasHashtagTextTrait;

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

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => $this->resource->moduleName(),
            'resource_name' => $this->resource->entityType(),
            'name'          => $this->resource->name,
            'description'   => $this->resource->description,
            'is_featured'   => $this->resource->is_featured,
            'is_sponsor'    => $this->resource->is_sponsor,
            'is_favorite'   => $this->resource->isFavorite($context),
            'privacy'       => $this->resource->privacy,
            'is_saved'      => PolicyGate::check(
                $this->resource->entityType(),
                'isSavedItem',
                [$context, $this->resource]
            ),
            'module_id'         => $this->resource->ownerId() != $this->resource->userId() ? $this->resource->ownerType() : $this->resource->entityType(),
            'item_id'           => $this->resource->ownerId() != $this->resource->userId() ? $this->resource->ownerId() : 0,
            'image'             => $this->resource->images,
            'statistic'         => $this->getStatistic(),
            'link'              => $this->resource->toLink(),
            'url'               => $this->resource->toUrl(),
            'user'              => new UserEntityDetail($this->resource->userEntity),
            'owner'             => new UserEntityDetail($this->resource->ownerEntity),
            'attachments'       => new AttachmentItemCollection($this->resource->attachments),
            'owner_type_name'   => __p("{$this->resource->ownerType()}::phrase.{$this->resource->ownerType()}"),
            'creation_date'     => $this->resource->created_at,
            'modification_date' => $this->resource->updated_at,
        ];
    }
}
