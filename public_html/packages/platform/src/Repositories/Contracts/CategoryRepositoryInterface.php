<?php

namespace MetaFox\Platform\Repositories\Contracts;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MetaFox\Platform\Contracts\User;

/**
 * Interface CategoryRepositoryInterface.
 */
interface CategoryRepositoryInterface
{
    /**
     * @param  User  $context
     * @param  array $attributes
     * @return Model
     */
    public function createCategory(User $context, array $attributes): Model;

    /**
     * @param User $context
     * @param int  $id
     *
     * @param array<string, mixed> $attributes
     *
     * @return Model
     * @throws AuthorizationException
     */
    public function updateCategory(User $context, int $id, array $attributes): Model;

    /**
     * @param  Model $category
     * @param  int   $newCategoryId
     * @return bool
     */
    public function deleteOrMoveToNewCategory(Model $category, int $newCategoryId): bool;

    /**
     * @return array<int, mixed>
     */
    public function getCategoriesForForm(): array;

    /**
     * @return array<int, mixed>
     */
    public function getCategoriesForStoreForm(?Model $category): array;

    /**
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @return Collection
     */
    public function getAllCategories(User $context, array $attributes): Collection;

    /**
     * @return Collection
     */
    public function getCategoryForFilter(): Collection;

    /**
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @throws AuthorizationException
     */
    public function viewForAdmin(User $context, array $attributes);

    /**
     * @return mixed
     */
    public function clearCache();

    /**
     * @param  ?Model $category
     * @param  int    $totalItem
     * @return void
     */
    public function incrementTotalItemCategories(?Model $category, int $totalItem): void;

    /**
     * @param  ?Model $category
     * @param  int    $totalItem
     * @return void
     */
    public function decrementTotalItemCategories(?Model $category, int $totalItem): void;

    /**
     * @param  array $orderIds
     * @return bool
     */
    public function orderCategories(array $orderIds): bool;

    public function toggleActive(int $id): Model;
}
