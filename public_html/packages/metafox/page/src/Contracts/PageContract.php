<?php

namespace MetaFox\Page\Contracts;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use MetaFox\Platform\Contracts\User;

interface PageContract
{
    /**
     * @param  string $content
     * @return array
     */
    public function getMentions(string $content): array;

    /**
     * @param  array      $ids
     * @return Collection
     */
    public function getPagesForMention(array $ids): Collection;

    /**
     * @param  User    $user
     * @return Builder
     */
    public function getPageBuilder(User $user): Builder;

    /**
     * @return array
     */
    public function getListTypes(): array;

    public function isFollowing(User $context, User $user): bool;
}
