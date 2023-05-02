<?php

namespace MetaFox\Platform\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Repositories\Contracts\CategoryRepositoryInterface;

/**
 * Trait HasApprove.
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
abstract class AbstractCategoryRepository extends AbstractRepository implements CategoryRepositoryInterface
{
    public function createCategory(User $context, array $attributes): Model
    {
        $attributes['is_active'] = Arr::get($attributes, 'is_active', 0);

        $parentId = Arr::get($attributes, 'parent_id');

        $attributes['level'] = 1;

        if ($parentId) {
            $parent = $this->find($parentId);

            $attributes['level'] += $parent->level;
        }

        $attributes['ordering'] = $this->getNextOrdering($attributes['level']);

        $category = $this->getModel()->newQuery()->create($attributes);

        $category->refresh();

        $this->clearCache();

        return $category;
    }

    protected function getNextOrdering(int $level): int
    {
        $currentCategory = $this->getModel()->newModelQuery()
            ->where([
                'level' => $level,
            ])
            ->orderByDesc('ordering')
            ->first();

        if (null === $currentCategory) {
            return 0;
        }

        return (int) $currentCategory->ordering + 1;
    }

    public function updateCategory(User $context, int $id, array $attributes): Model
    {
        $category    = $this->find($id);
        $oldParentId = $category->parent_id;
        $newParentId = Arr::get($attributes, 'parent_id');

        if ($newParentId !== null && $newParentId !== $oldParentId) {
            $this->moveToNewCategory($category, $newParentId);
        }

        if ($newParentId == null) {
            $this->decrementTotalItemCategories($category?->parentCategory, $category->total_item);
            $attributes['level'] = 1;
        }

        $category->fill($attributes)->save();
        $category->refresh();
        $this->clearCache();

        return $category;
    }

    public function deleteOrMoveToNewCategory(Model $category, int $newCategoryId): bool
    {
        if ($newCategoryId > 0) {
            $this->moveToNewCategory($category, $newCategoryId, true);

            return (bool) $category->delete();
        }

        $this->deleteAllBelongTo($category);

        return (bool) $category->delete();
    }

    public function getCategoriesForForm(): array
    {
        return $this->getModel()->newQuery()
            ->select('id as value', 'name as label', 'parent_id', 'is_active', 'level', 'ordering')
            ->where('is_active', MetaFoxConstant::IS_ACTIVE)
            ->orderBy('ordering')
            ->get()
            ->toArray();
    }

    public function getCategoriesForStoreForm(?Model $category): array
    {
        $query = $this->getModel()->newQuery()
            ->where('is_active', MetaFoxConstant::IS_ACTIVE);
        $query->where('level', '<', MetaFoxConstant::MAX_CATEGORY_LEVEL);

        if (null !== $category) {
            $query->where('id', '<>', $category->entityId());
            $query->where('level', '<=', $category->level);

            if ($category->subCategories()->exists()) {
                $query->where('level', '<=', $category->level);
            }

            if ($category->subCategories()->exists() && !$category->parentCategory()->exists()) {
                return [];
            }
        }

        return $query->select('id as value', 'name as label', 'parent_id', 'is_active', 'level')
            ->orderBy('ordering')
            ->get()
            ->toArray();
    }

    public function getCategoryForFilter(): Collection
    {
        return $this->getModel()->newQuery()
            ->with([
                'subCategories' => function (HasMany $q) {
                    $q->where('is_active', MetaFoxConstant::IS_ACTIVE)
                        ->orderBy('ordering');
                },
            ])
            ->whereNull('parent_id')
            ->where('is_active', MetaFoxConstant::IS_ACTIVE)
            ->orderBy('ordering')
            ->get()
            ->collect();
    }

    public function getAllCategories(User $context, array $attributes): Collection
    {
        $query    = $this->getModel()->newQuery();
        $search   = Arr::get($attributes, 'q');
        $level    = Arr::get($attributes, 'level', 1);
        $relation = [
            'subCategories' => function (HasMany $q) {
                $q->where('is_active', MetaFoxConstant::IS_ACTIVE)
                    ->orderBy('ordering');
            },
        ];

        if ($search !== null) {
            return $query->where('name', $this->likeOperator(), '%' . $search . '%')->get()->collect();
        }

        if (array_key_exists('id', $attributes)) {
            return $query->where('id', '=', $attributes['id'])->get()->collect();
        }

        $key = $this->getModel()->entityType() . '_get_all';

        if ($level != 0) {
            $key = $this->getModel()->entityType() . '_level';
            $query->where('level', $level);
        }

        return Cache::rememberForever($key, function () use ($query, $relation) {
            return $query
                ->with($relation)
                ->where('is_active', MetaFoxConstant::IS_ACTIVE)
                ->orderBy('ordering')
                ->get()
                ->collect();
        });
    }

    public function viewForAdmin(User $context, array $attributes)
    {
        $parentId = Arr::get($attributes, 'parent_id');

        $query = $this->getModel()->newModelQuery();

        if (null === $parentId) {
            $query->whereNull('parent_id');
        }

        if (is_numeric($parentId)) {
            $query->where('parent_id', '=', $parentId);
        }

        return $query
            ->orderBy('ordering')
            ->with(['subCategories', 'parentCategory'])
            ->get();
    }

    public function clearCache()
    {
        $cacheName           = $this->getModel()->entityType() . '_get_all';
        $cacheNameNullParent = $this->getModel()->entityType() . '_level';
        Cache::forget($cacheName);
        Cache::forget($cacheNameNullParent);
    }

    /**
     * @inheritDoc
     */
    public function incrementTotalItemCategories(?Model $category, int $totalItem): void
    {
        if (!$category instanceof Model) {
            return;
        }

        do {
            $total = $totalItem + $category->total_item;
            $category->update(['total_item' => $total]);
            $category = $category?->parentCategory;
        } while ($category);
    }

    /**
     * @inheritDoc
     */
    public function decrementTotalItemCategories(?Model $category, int $totalItem): void
    {
        if (!$category instanceof Model) {
            return;
        }

        do {
            $total = $category->total_item - $totalItem;
            $category->update(['total_item' => $total]);
            $category = $category?->parentCategory;
        } while ($category);
    }

    public function orderCategories(array $orderIds): bool
    {
        $categories = $this->getModel()->newQuery()
            ->whereIn('id', $orderIds)
            ->get()
            ->keyBy('id');

        if (!$categories->count()) {
            return true;
        }

        $ordering = 1;

        foreach ($orderIds as $orderId) {
            $category = $categories->get($orderId);

            if (!is_object($category)) {
                continue;
            }

            $category->update(['ordering' => $ordering++]);
        }

        return true;
    }

    public function toggleActive(int $id): Model
    {
        $item = $this->find($id);

        $item->update(['is_active' => $item->is_active ? 0 : 1]);

        $this->clearCache();

        return $item;
    }
}
