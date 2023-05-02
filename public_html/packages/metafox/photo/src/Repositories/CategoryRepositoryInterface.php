<?php

namespace MetaFox\Photo\Repositories;

use Illuminate\Auth\Access\AuthorizationException;
use MetaFox\Photo\Models\Category;
use MetaFox\Platform\Contracts\User;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface CategoryRepositoryInterface.
 * @mixin BaseRepository
 */
interface CategoryRepositoryInterface
{
    /**
     * @return string
     */
    public function model(): string;

    /**
     * @param User $context
     * @param int  $id
     *
     * @return Category
     * @throws AuthorizationException
     */
    public function viewCategory(User $context, int $id): Category;

    /**
     * @param User                 $context
     * @param int                  $id
     * @param array<string, mixed> $attributes
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function deleteCategory(User $context, int $id, array $attributes): bool;

    /**
     * @param Category $category
     *
     * @return bool
     */
    public function deleteAllBelongTo(Category $category): bool;

    /**
     * @param Category $category
     * @param int      $newCategoryId
     * @param bool     $isDelete
     */
    public function moveToNewCategory(Category $category, int $newCategoryId, bool $isDelete = false): void;
}
