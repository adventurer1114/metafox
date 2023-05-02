<?php

namespace MetaFox\Contact\Repositories;

use MetaFox\Contact\Models\Category;

/**
 * --------------------------------------------------------------------------
 * Code Generator
 * --------------------------------------------------------------------------
 * stub: src/Repositories/CategoryRepositoryInterface.stub.
 */

/**
 * Interface CategoryRepositoryInterface.
 * @method Category getModel()
 * @method Category find($id, $columns = ['*'])()
 */
interface CategoryRepositoryInterface
{
    /**
     * @param  Category $category
     * @param  int      $newCategoryId
     * @param  bool     $isDelete
     * @return void
     */
    public function moveToNewCategory(Category $category, int $newCategoryId, bool $isDelete = false): void;

    /**
     * @param  Category $category
     * @return void
     */
    public function deleteCategory(Category $category): void;
}
