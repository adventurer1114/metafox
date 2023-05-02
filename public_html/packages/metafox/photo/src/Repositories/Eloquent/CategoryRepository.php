<?php

namespace MetaFox\Photo\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Relations\HasMany;
use MetaFox\Photo\Jobs\DeleteCategoryJob;
use MetaFox\Photo\Models\Category;
use MetaFox\Photo\Models\Photo;
use MetaFox\Photo\Repositories\CategoryRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractCategoryRepository;

/**
 * Class CategoryRepository.
 * @property Category $model
 * @method   Category getModel()
 * @method   Category find($id)
 */
class CategoryRepository extends AbstractCategoryRepository implements CategoryRepositoryInterface
{
    public function model(): string
    {
        return Category::class;
    }

    public function viewCategory(User $context, int $id): Category
    {
        $relation = [
            'subCategories' => function (HasMany $query) {
                $query->where('is_active', Category::IS_ACTIVE);
            },
        ];

        return $this->with($relation)->find($id);
    }

    public function deleteCategory(User $context, int $id, array $attributes): bool
    {
        $category = $this->find($id);

        $newCategoryId = $attributes['new_category_id'] ?? 0;

        DeleteCategoryJob::dispatch($category, $newCategoryId);

        $this->clearCache();
        return true;
    }

    public function moveToNewCategory(Category $category, int $newCategoryId, bool $isDelete = false): void
    {
        $totalItem = $category->total_item;
        $parent = $category?->parentCategory;
        $this->decrementTotalItemCategories($parent, $totalItem);

        $newCategory = $this->find($newCategoryId);
        $photoIds = $category->photos()->pluck('photos.id')->toArray();

        //Move photo
        if (!empty($photoIds) && $isDelete) {
            $newCategory->photos()->sync($photoIds, false);
        }

        //update parent_id
        Category::query()->where('parent_id', '=', $category->entityId())->update([
            'parent_id' => $newCategory->entityId(),
            'level'     => $newCategory->level + 1,
        ]);
        $this->incrementTotalItemCategories($newCategory, $totalItem);
    }

    public function deleteAllBelongTo(Category $category): bool
    {
        $category->photos()->each(function (Photo $photo) {
            $photo->delete();
        });

        $category->subCategories()->each(function (Category $item) {
            DeleteCategoryJob::dispatch($item, 0);
        });

        return true;
    }
}
