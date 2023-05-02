<?php

namespace MetaFox\Platform\Support\Repository;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasPolicy;
use MetaFox\Platform\Contracts\User;

/**
 * Trait HasSponsor.
 */
trait HasSponsor
{
    /**
     * @param User $context
     * @param int  $id
     * @param int  $sponsor
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function sponsor(User $context, int $id, int $sponsor): bool
    {
        $resource = $this->find($id);

        if ($resource instanceof HasPolicy) {
            gate_authorize($context, 'sponsor', $resource, $resource, $sponsor);
        }

        return $resource->update(['is_sponsor' => $sponsor]);
    }

    /**
     * @param Content $model
     *
     * @return bool
     */
    public function isSponsor(Content $model): bool
    {
        if (!$model instanceof \MetaFox\Platform\Contracts\HasSponsor) {
            return false;
        }

        return $model->is_sponsor == 1;
    }

    public function getSponsoredItem(array $notInIds, ?int $sponsorStart = null, array $with = []): ?Content
    {
        $query = $this->getModel()->newModelQuery()
            ->with($with)
            ->where('is_sponsor', '=', 1)
            ->where('id', '<', $sponsorStart ?? 0)
            ->inRandomOrder();

        if (!empty($notInIds)) {
            $query->whereNotIn('id', $notInIds);
        }

        return $query->first();
    }

    public function transformPaginatorWithSponsor(Paginator $paginator, string $cacheKey, int $cacheTime, string $primaryKey = 'id', array $with = []): Paginator
    {
        $items = $paginator->getCollection();

        $items = $this->transformCollectionWithSponsor($items, $cacheKey, $cacheTime, $primaryKey, $with);

        $paginator->setCollection($items);

        return $paginator;
    }

    public function transformCollectionWithSponsor(Collection $collection, string $cacheKey, int $cacheTime, string $primaryKey = 'id', array $with = []): Collection
    {
        $itemIds = $collection->pluck($primaryKey)->toArray();

        $lastItem = $collection->last();
        $lastItemId = $lastItem instanceof Entity ? $lastItem->entityId() : null;

        // Append to the first position in the collection
        $sponsorItem = Cache::remember($cacheKey, $cacheTime, function () use ($itemIds, $lastItemId, $with) {
            $item = $this->getSponsoredItem($itemIds, $lastItemId, $with);
            if ($item === null) {
                return false;
            }

            return $item;
        });

        if ($sponsorItem instanceof Content) {
            $collection->prepend($sponsorItem);
        }

        return $collection;
    }
}
