<?php

namespace MetaFox\Marketplace\Http\Resources\v1\Category\Admin;

use MetaFox\Marketplace\Models\Category as Model;
use MetaFox\Marketplace\Repositories\CategoryRepositoryInterface;

/**
 * Class UpdateCategoryForm.
 * @property Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class UpdateCategoryForm extends StoreCategoryForm
{
    public function boot(CategoryRepositoryInterface $repository, ?int $id = null): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $model = $this->resource;

        $this->asPut()->title('Edit Category')
            ->action(url_utility()->makeApiUrl('admincp/marketplace/category/' . $this->resource->id))
            ->setValue([
                'name'      => $model->name,
                'is_active' => $model->is_active,
                'ordering'  => $model->ordering,
                'parent_id' => $model->parent_id,
                'name_url'  => $model->name_url,
            ]);
    }

    protected function isEdit(): bool
    {
        return true;
    }

    protected function isDisabled(): bool
    {
        return $this->resource->is_default;
    }
}
