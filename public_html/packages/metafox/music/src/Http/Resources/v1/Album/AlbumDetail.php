<?php

namespace MetaFox\Music\Http\Resources\v1\Album;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use MetaFox\Core\Http\Resources\v1\Attachment\AttachmentItemCollection;
use MetaFox\Hashtag\Traits\HasHashtagTextTrait;
use MetaFox\Music\Http\Resources\v1\Song\SongItemCollection;
use MetaFox\Music\Http\Resources\v1\Song\SongPlayCollection;
use MetaFox\Music\Models\Album;
use MetaFox\Music\Policies\AlbumPolicy;
use MetaFox\Music\Repositories\AlbumRepositoryInterface;
use MetaFox\Music\Support\Browse\Traits\Album\StatisticTrait;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\Traits\Helpers\IsLikedTrait;
use MetaFox\Platform\Traits\Http\Resources\HasExtra;
use MetaFox\Platform\Traits\Http\Resources\HasFeedParam;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\Video\Support\Browse\Traits\Video\HandleContentTrait;

/**
 * Class AlbumDetail.
 * @property Album $resource
 */
class AlbumDetail extends JsonResource
{
    use StatisticTrait;
    use HasExtra;
    use HasFeedParam;
    use IsLikedTrait;
    use HandleContentTrait;
    use HasHashtagTextTrait;

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

        $shortDescription = $text = '';

        if ($this->resource->albumText) {
            $shortDescription = parse_output()->getDescription($this->resource->albumText->text_parsed);
            $text             = $this->getTransformContent($this->resource->albumText->text_parsed);
            $text             = parse_output()->parse($text);
        }

        return [
            'id'              => $this->resource->entityId(),
            'module_name'     => $this->resource->moduleName(),
            'resource_name'   => $this->resource->entityType(),
            'name'            => $this->resource->name,
            'description'     => $shortDescription,
            'text'            => $text,
            'is_featured'     => $this->resource->is_featured,
            'is_sponsor'      => $this->resource->is_sponsor,
            'is_favorite'     => $this->resource->isFavorite($context),
            'privacy'         => $this->resource->privacy,
            'is_liked'        => $this->isLike($context),
            'is_saved'        => PolicyGate::check($this->resource->entityType(), 'isSavedItem', [$context, $this->resource]),
            'module_id'       => $this->resource->ownerId() != $this->resource->userId() ? $this->resource->ownerType() : $this->resource->entityType(),
            'item_id'         => $this->resource->ownerId() != $this->resource->userId() ? $this->resource->ownerId() : 0,
            'image'           => $this->resource->images,
            'statistic'       => $this->getStatistic(),
            'link'            => $this->resource->toLink(),
            'url'             => $this->resource->toUrl(),
            'user'            => new UserEntityDetail($this->resource->userEntity),
            'owner'           => new UserEntityDetail($this->resource->ownerEntity),
            'attachments'     => new AttachmentItemCollection($this->resource->attachments),
            'owner_type_name' => __p("{$this->resource->ownerType()}::phrase.{$this->resource->ownerType()}"),
            'extra'           => $this->getExtra(),
            'view_id'         => $this->resource->view_id,
            'creation_date'   => $this->resource->created_at,
            'year'            => $this->resource->year,
            'feed_param'      => $this->getFeedParams(),
            'initial_songs'   => $this->getInitialSongs(),
        ];
    }

    protected function getInitialSongs(): ResourceCollection
    {
        $context = user();

        if (!policy_check(AlbumPolicy::class, 'view', $context, $this->resource)) {
            return new SongPlayCollection([]);
        }

        $songs = resolve(AlbumRepositoryInterface::class)->viewAlbumItems($context, $this->resource->entityId());

        return new SongPlayCollection($songs);
    }
}
