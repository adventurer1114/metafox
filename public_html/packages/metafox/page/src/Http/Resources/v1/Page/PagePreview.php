<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Page\Models\Page as Model;
use MetaFox\Page\Support\Facade\PageMembership;
use MetaFox\Platform\Contracts\ResourceText;

/**
 * Class PagePreview.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PagePreview extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    protected function getStatistic(): array
    {
        return [
            'total_like'  => $this->resource->total_member,
            'total_admin' => $this->resource->total_admin,
        ];
    }

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

        $pageText    = $this->resource->pageText;
        $description = '';

        if ($pageText instanceof ResourceText) {
            $description = parse_output()->getDescription($pageText->text_parsed);
        }

        return [
            'id'                   => $this->resource->entityId(),
            'module_name'          => $this->resource->entityType(),
            'resource_name'        => $this->resource->entityType(),
            'full_name'            => $this->resource->name,
            'avatar'               => $this->resource->avatar,
            'cover'                => $this->resource->cover,
            'cover_photo_position' => $this->resource->cover_photo_position,
            'statistic'            => $this->getStatistic(),
            'is_liked'             => $this->resource->isMember($context),
            'privacy'              => $this->resource->privacy,
            'description'          => $description,
            'membership'           => PageMembership::getMembership($this->resource, $context),
            'location'             => [
                'name'      => $this->resource->location_name,
                'longitude' => $this->resource->location_longitude,
                'latitude'  => $this->resource->location_latitude,
            ],
        ];
    }
}
