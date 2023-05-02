<?php

namespace MetaFox\Music\Http\Resources\v1\Genre\Admin;

use MetaFox\Music\Models\Genre as Model;
use MetaFox\Music\Repositories\GenreRepositoryInterface;

/**
 * Class UpdateGenreForm.
 * @property Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class UpdateGenreForm extends StoreGenreForm
{
    public function boot(GenreRepositoryInterface $repository, ?int $id = null): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $model = $this->resource;

        $this->asPut()->title(__p('music::phrase.edit_genre'))
            ->action(url_utility()->makeApiUrl('admincp/music/genre/' . $this->resource->id))
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
