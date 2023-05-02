<?php

namespace MetaFox\Event\Http\Resources\v1\Category\Admin;

use MetaFox\Event\Models\Category as Model;
use MetaFox\Event\Repositories\CategoryRepositoryInterface;

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

        $this->action(url_utility()->makeApiUrl('admincp/event/category/' . $this->resource->id))
            ->asPut()
            ->title(__p('core::phrase.edit_category'))
            ->setValue([
                'name'      => $model->name,
                'is_active' => $model->is_active,
                'ordering'  => $model->ordering,
                'parent_id' => $model->parent_id,
                'name_url'  => $model->name_url,
            ]);
    }

    protected function isDisabled(): bool
    {
        return $this->resource->is_default;
    }
}
