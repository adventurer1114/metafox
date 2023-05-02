<?php

namespace MetaFox\Page\Repositories\Eloquent;

use MetaFox\Page\Jobs\DeletePageCategoryJob;
use MetaFox\Page\Models\Category;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Repositories\AbstractCategoryRepository;

/**
 * Class PageCategoryRepository.
 * @method Category getModel()
 * @method Category find($id, $columns = ['*'])
 */
class PageCategoryRepository extends AbstractCategoryRepository implements PageCategoryRepositoryInterface
{
    public function model(): string
    {
        return Category::class;
    }

    public function viewCategory(User $context, int $id): Category
    {
        return $this->find($id);
    }

    public function deleteCategory(User $context, int $id, int $newCategoryId, int $newTypeId): bool
    {
        $category = $this->find($id);

        DeletePageCategoryJob::dispatchSync($category, $newCategoryId, $newTypeId);

        $this->clearCache();

        return true;
    }

    public function moveToNewCategory(Category $category, int $newCategoryId, bool $isDelete = false): void
    {
        $totalItem = $category->total_item;
        $parent    = $category?->parentCategory;
        $this->decrementTotalItemCategories($parent, $totalItem);

        $newCategory = $this->find($newCategoryId);
        $pageIds     = $category->pages()->pluck('pages.id')->toArray();

        if (!empty($pageIds) && $isDelete) {
            //Move page
            Page::query()->whereIn('category_id', $pageIds)
                ->update([
                    'category_id' => $newCategoryId,
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
        $category->pages()->each(function (Page $page) {
            $page->delete();
        });

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCategoryDefault(): ?Category
    {
        $defaultCategory = Settings::get('page.default_category');

        return $this->getModel()->newModelQuery()
            ->where('id', $defaultCategory)->first();
    }
}
