<?php

namespace MetaFox\Photo\Http\Resources\v1\Photo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Photo\Models\Photo;
use MetaFox\Photo\Repositories\PhotoRepositoryInterface;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/**
 * Class PhotoEmbed.
 * @property Photo $resource
 */
class PhotoEmbed extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request): array
    {
        $this->resource->loadMissing('photoInfo', 'fileItem');

        $context = user();

        $fileItem = $this->resource->fileItem;

        $repository = resolve(PhotoRepositoryInterface::class);

        $taggedFriends = $repository->getTaggedFriends($context, $this->resource->entityId());

        return [
            'id'             => $this->resource->entityId(),
            'module_name'    => Photo::ENTITY_TYPE,
            'resource_name'  => $this->resource->entityType(),
            'mature'         => $this->resource->mature,
            'width'          => $fileItem?->width,
            'height'         => $fileItem?->height,
            'user'           => new UserEntityDetail($this->resource->userEntity),
            'owner'          => new UserEntityDetail($this->resource->ownerEntity),
            'image'          => $this->resource->images,
            'is_featured'    => $this->resource->is_featured,
            'is_sponsor'     => $this->resource->is_sponsor,
            'text'           => $this->resource->content,
            'tagged_friends' => new PhotoTaggedFriendCollection($taggedFriends),
        ];
    }
}
