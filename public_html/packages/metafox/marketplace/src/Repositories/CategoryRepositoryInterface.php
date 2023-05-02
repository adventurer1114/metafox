<?php

namespace MetaFox\Marketplace\Repositories;

use Illuminate\Auth\Access\AuthorizationException;
use MetaFox\Marketplace\Models\Category;
use MetaFox\Platform\Contracts\User;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface CategoryRepositoryInterface.
 * @mixin BaseRepository
 * @method Category getModel()
 * @method Category find($id, $columns = ['*'])()
 */
interface CategoryRepositoryInterface
{
    /**
     * @param User $context
     * @param int  $id
     *
     * @return Category
     * @throws AuthorizationException
     */
    public function viewCategory(User $context, int $id): Category;

    /**
     * @param User $context
     * @param int  $id
     * @param int  $newCategoryId
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function deleteCategory(User $context, int $id, int $newCategoryId): bool;

    /**
     * @param  Category $category
     * @param  int      $newCategoryId
     * @param  bool     $isDelete
     * @return void
     */
    public function moveToNewCategory(Category $category, int $newCategoryId, bool $isDelete = false): void;

    /**
     * @param  Category $category
     * @return bool
     */
    public function deleteAllBelongTo(Category $category): bool;

    /**
     * @return Category|null
     */
    public function getCategoryDefault(): ?Category;
}
