<?php

namespace MetaFox\Video\Http\Resources\v1\Video;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Hashtag\Traits\HasHashtagTextTrait;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\Video\Models\Video;
use MetaFox\Video\Support\Browse\Traits\Video\HandleContentTrait;

/**
 * Class VideoEmbed.
 * @property Video $resource
 */
class VideoEmbed extends JsonResource
{
    use HasHashtagTextTrait;
    use HasStatistic;
    use HandleContentTrait;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request): array
    {
        $content = match ($this->resource->group_id > 0) {
            true  => $this->handleContentForUpload(),
            false => $this->handleContentForLink(),
        };

        if ($this->resource->group_id == 0) {
            if (is_string($content)) {
                $content = $this->getTransformContent($content);
            }
        }

        return [
            'id'                => $this->resource->entityId(),
            'module_name'       => $this->resource->moduleName(),
            'resource_name'     => $this->resource->entityType(),
            'is_processing'     => $this->resource->is_processing,
            'image'             => $this->resource->images,
            'destination'       => $this->resource->video_path,
            'title'             => $this->resource->title,
            'description'       => parse_output()->getDescription($content),
            'embed_code'        => $this->resource->embed_code,
            'video_url'         => $this->resource->video_url,
            'duration'          => $this->resource->duration,
            'resolution_x'      => $this->resource->resolution_x,
            'resolution_y'      => $this->resource->resolution_y,
            'user'              => new UserEntityDetail($this->resource->userEntity),
            'owner'             => new UserEntityDetail($this->resource->ownerEntity),
            'link'              => $this->resource->toLink(),
            'url'               => $this->resource->toUrl(),
            'is_featured'       => $this->resource->is_featured,
            'is_sponsor'        => $this->resource->is_sponsor,
            'is_sponsored_feed' => $this->resource->sponsor_in_feed,
            'statistic'         => $this->getStatistic(),
            'text'              => $content,
        ];
    }
}
