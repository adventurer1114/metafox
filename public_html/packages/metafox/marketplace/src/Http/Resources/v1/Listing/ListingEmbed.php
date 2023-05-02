<?php

namespace MetaFox\Marketplace\Http\Resources\v1\Listing;

use Illuminate\Http\Request;
use MetaFox\Marketplace\Models\Listing;
use MetaFox\Marketplace\Policies\ListingPolicy;
use MetaFox\Marketplace\Support\Facade\Listing as Facade;

/**
 * Class ListingEmbed.
 * @property Listing $resource
 */
class ListingEmbed extends ListingDetail
{
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
        $item = $this->resource;

        $context = user();

        $canViewListing = policy_check(ListingPolicy::class, 'view', $context, $item);

        return [
            'id'                => $item->entityId(),
            'module_name'       => $this->getModuleName(),
            'resource_name'     => $item->entityType(),
            'title'             => $item->title,
            'categories'        => $this->getCategories(),
            'short_description' => $this->getShortDescription(),
            'description'       => $this->getDescription(),
            'price'             => $this->getUserPrice(),
            'image'             => $item->images,
            'user'              => $this->getUserEntity(),
            'location'          => $item->toLocationObject(),
            'privacy'           => $item->privacy,
            'tags'              => $item->tags,
            'link'              => !$canViewListing || $item->trashed() ? null : $item->toLink(),
            'url'               => !$canViewListing || $item->trashed() ? null : $item->toUrl(),
            'router'            => !$canViewListing || $item->trashed() ? null : $item->toRouter(),
            'is_featured'       => $item->is_featured,
            'is_sponsor'        => $item->is_sponsor,
            'is_pending'        => !$item->is_approved,
            'is_expired'        => $this->resource->is_expired,
            'is_sold'           => $this->resource->is_sold,
            'is_free'           => $this->isFree(),
            'statistic'         => $this->getStatistic(),
            'expires_label'     => Facade::getExpiredLabel($this->resource, $this->isListing()),
        ];
    }

    protected function isListing(): bool
    {
        return true;
    }
}
