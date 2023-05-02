<?php

namespace MetaFox\Music\Http\Resources\v1\Album;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Core\Http\Resources\v1\Attachment\AttachmentItemCollection;
use MetaFox\Hashtag\Traits\HasHashtagTextTrait;
use MetaFox\Music\Models\Album;
use MetaFox\Music\Support\Browse\Traits\Album\StatisticTrait;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\Video\Support\Browse\Traits\Video\HandleContentTrait;

/**
 * Class AlbumEmbed.
 * @property Album $resource
 */
class AlbumEmbed extends JsonResource
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

        $shortDescription = $text = '';
        if ($this->resource->albumText) {
            $shortDescription = parse_output()->getDescription($this->resource->albumText->text_parsed);
            $text             = $this->getTransformContent($this->resource->albumText->text_parsed);
            $text             = parse_output()->parse($text);
        }

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => $this->resource->moduleName(),
            'resource_name' => $this->resource->entityType(),
            'name'          => $this->resource->name,
            'description'   => $shortDescription,
            'text'          => $text,
            'is_featured'   => $this->resource->is_featured,
            'is_sponsor'    => $this->resource->is_sponsor,
            'is_favorite'   => $this->resource->isFavorite($context),
            'privacy'       => $this->resource->privacy,
            'is_saved'      => PolicyGate::check(
                $this->resource->entityType(),
                'isSavedItem',
                [$context, $this->resource]
            ),
            'image'           => $this->resource->images,
            'statistic'       => $this->getStatistic(),
            'link'            => $this->resource->toLink(),
            'url'             => $this->resource->toUrl(),
            'user'            => new UserEntityDetail($this->resource->userEntity),
            'owner'           => new UserEntityDetail($this->resource->ownerEntity),
            'attachments'     => new AttachmentItemCollection($this->resource->attachments),
            'owner_type_name' => __p("{$this->resource->ownerType()}::phrase.{$this->resource->ownerType()}"),
            'view_id'         => $this->resource->view_id,
            'creation_date'   => $this->resource->created_at,
            'year'            => $this->resource->year,
        ];
    }
}
