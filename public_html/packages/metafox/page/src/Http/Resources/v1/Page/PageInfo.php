<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Page\Http\Resources\v1\PageCategory\PageCategoryEmbed;
use MetaFox\Page\Http\Resources\v1\Traits\PageHasExtra;
use MetaFox\Page\Models\Page as Model;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/**
 * Class PageInfo.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PageInfo extends JsonResource
{
    use PageHasExtra;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => $this->resource->entityType(),
            'resource_name' => 'page_info',
            'text'          => $this->resource->pageText->text,
            'text_parsed'   => $this->resource->pageText->text_parsed,
            'description'   => $this->resource->pageText->text_parsed,
            'total_like'    => $this->resource->total_member,
            'external_link' => $this->resource->external_link,
            'phone'         => null,
            //@todo: In the next version, we will check validation of this field and display it again
            'location'          => $this->resource->location_name,
            'user'              => new UserEntityDetail($this->resource->userEntity),
            'creation_date'     => $this->resource->created_at,
            'modification_date' => $this->resource->updated_at,
            'link'              => $this->resource->toLink(),
            'url'               => $this->resource->toUrl(),
            'extra'             => $this->getExtra(),
            'privacy'           => $this->resource->privacy,
            'category'          => new PageCategoryEmbed($this->resource->category),
        ];
    }
}
