<?php

namespace MetaFox\Group\Repositories\Eloquent;

use MetaFox\Group\Jobs\DeleteCategoryJob;
use MetaFox\Group\Models\Category;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Repositories\CategoryRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Repositories\AbstractCategoryRepository;

/**
 * Class GroupCategoryRepository.
 * @method Category getModel()
 * @method Category find($id, $columns = ['*'])
 * @inore
 * @codeCoverageIgnore
 */
class CategoryRepository extends AbstractCategoryRepository implements CategoryRepositoryInterface
{
    public function model(): string
    {
        return Category::class;
    }

    public function viewCategory(User $context, int $id): Category
    {
        return $this->find($id);
    }

    public function deleteCategory(User $context, int $id, int $newCategoryId): bool
    {
        $category = $this->find($id);

        DeleteCategoryJob::dispatchSync($category, $newCategoryId);

        $this->clearCache();

        return true;
    }

    public function moveToNewCategory(Category $category, int $newCategoryId, bool $isDelete = false): void
    {
        $totalItem = $category->total_item;
        $parent    = $category?->parentCategory;
        $this->decrementTotalItemCategories($parent, $totalItem);

        $newCategory = $this->find($newCategoryId);
        $groupIds    = $category->groups()->pluck('groups.id')->toArray();

        //Move groups
        if (!empty($groupIds) && $isDelete) {
            Group::query()->whereIn('id', $groupIds)
                ->update([
                    'category_id' => $newCategory->entityId(),
                ]);
        }

        //update parent_id
        Category::query()
            ->where('parent_id', '=', $category->entityId())
            ->update([
                'parent_id' => $newCategory->entityId(),
                'level'     => $newCategory->level + 1,
            ]);
        $this->incrementTotalItemCategories($newCategory, $totalItem);
    }

    public function deleteAllBelongTo(Category $category): bool
    {
        $category->groups()->each(function (Group $group) {
            $group->delete();
        });

        $category->subCategories()->each(function (Category $item) {
            DeleteCategoryJob::dispatch($item, 0);
        });

        return true;
    }

    public function getCategoryDefault(): ?Category
    {
        $defaultCategory = Settings::get('group.default_category');

        return $this->getModel()->newModelQuery()
            ->where('id', $defaultCategory)->first();
    }
}
