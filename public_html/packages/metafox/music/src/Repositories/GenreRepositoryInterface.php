<?php

namespace MetaFox\Music\Repositories;

use Illuminate\Support\Collection;
use MetaFox\Platform\Contracts\User;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface GenreRepositoryInterface.
 * @mixin BaseRepository
 */
interface GenreRepositoryInterface
{
    public function deleteCategory(User $context, int $id, int $newCategoryId): bool;
}
