<?php

namespace MetaFox\Contact\Repositories\Eloquent;

use MetaFox\Contact\Models\Category;
use MetaFox\Contact\Repositories\CategoryRepositoryInterface;
use MetaFox\Platform\Repositories\AbstractCategoryRepository;

/**
 * --------------------------------------------------------------------------
 * Code Generator
 * --------------------------------------------------------------------------
 * stub: src/Repositories/Eloquent/CategoryRepository.stub.
 */

/**
 * Class CategoryRepository.
 * @property Category $model
 * @method   Category getModel()
 * @method   Category find($id, $columns = ['*'])()
 */
class CategoryRepository extends AbstractCategoryRepository implements CategoryRepositoryInterface
{
    public function model(): string
    {
        return Category::class;
    }

    public function moveToNewCategory(Category $category, int $newCategoryId, bool $isDelete = false): void
    {
        $totalItem = $category->total_item;
        $parent    = $category?->parentCategory;
        $this->decrementTotalItemCategories($parent, $totalItem);

        $newCategory = $this->find($newCategoryId);

        //update parent_id
        Category::query()->where('parent_id', '=', $category->entityId())
            ->update([
                'parent_id' => $newCategory->entityId(),
            ]);

        $this->incrementTotalItemCategories($newCategory, $totalItem);
    }

    public function deleteCategory(Category $category): void
    {
        $category->subCategories()->each(function (Category $item) {
            $this->deleteCategory($item);
        });

        $category->delete();
    }
}
