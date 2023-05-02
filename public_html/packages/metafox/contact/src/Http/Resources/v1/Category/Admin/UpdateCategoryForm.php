<?php

namespace MetaFox\Contact\Http\Resources\v1\Category\Admin;

use MetaFox\Contact\Models\Category as Model;
use MetaFox\Contact\Repositories\CategoryRepositoryInterface;

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

        $this->asPut()->title(__p('core::phrase.edit_category'))
            ->action(url_utility()->makeApiUrl('admincp/contact/category/' . $this->resource->id))
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
        return true;
    }
}
