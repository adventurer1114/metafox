<?php

namespace MetaFox\Page\Http\Resources\v1\PageCategory\Admin;

use MetaFox\Page\Models\Category as Model;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;

/**
 * Class UpdateCategoryForm.
 * @property Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class UpdateCategoryForm extends StoreCategoryForm
{
    public function boot(PageCategoryRepositoryInterface $repository, ?int $id = null): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $model = $this->resource;

        $this->action(url_utility()->makeApiUrl('admincp/page/category/' . $this->resource->id))
            ->asPut()
            ->title(__p('core::phrase.edit_category'))
            ->setValue([
                'name'      => $model->name,
                'is_active' => $model->is_active,
                'ordering'  => $model->ordering,
                'parent_id' => $model->parent_id,
            ]);
    }

    protected function isDisabled(): bool
    {
        return $this->resource->is_default;
    }
}
