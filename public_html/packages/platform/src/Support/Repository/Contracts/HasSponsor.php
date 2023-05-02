<?php

namespace MetaFox\Platform\Support\Repository\Contracts;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;

/**
 * Interface HasSponsor.
 */
interface HasSponsor
{
    /**
     * @param User $context
     * @param int  $id
     * @param int  $sponsor
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function sponsor(User $context, int $id, int $sponsor): bool;

    /**
     * @param Content $model
     *
     * @return bool
     */
    public function isSponsor(Content $model): bool;

    /**
     * @param array<int>   $notInIds
     * @param int|null     $sponsorStart
     * @param array<mixed> $with
     *
     * @return Content | null
     */
    public function getSponsoredItem(array $notInIds, ?int $sponsorStart = null, array $with = []): ?Content;

    /**
     * @param Collection $collection
     * @param string     $cacheKey
     * @param int        $cacheTime
     * @param string     $primaryKey
     * @param string[]   $with
     *
     * @return Collection
     */
    public function transformCollectionWithSponsor(Collection $collection, string $cacheKey, int $cacheTime, string $primaryKey = 'id', array $with = []): Collection;

    /**
     * @param Paginator                               $paginator
     * @param string                                  $cacheKey
     * @param int                                     $cacheTime
     * @param string                                  $primaryKey
     * @param array<int, string>|array<string, mixed> $with
     *
     * @return Paginator
     */
    public function transformPaginatorWithSponsor(Paginator $paginator, string $cacheKey, int $cacheTime, string $primaryKey = 'id', array $with = []): Paginator;
}
