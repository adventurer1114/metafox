<?php

namespace MetaFox\Music\Http\Resources\v1\Genre\Admin;

use MetaFox\Music\Models\Genre as Model;
use MetaFox\Form\Html\AbstractDestroyCategoryForm;
use MetaFox\Music\Repositories\GenreRepositoryInterface;

/**
 * Class DestroyGenreForm.
 * @property Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class DestroyGenreForm extends AbstractDestroyCategoryForm
{
    public function boot(GenreRepositoryInterface $repository, ?int $id = null): void
    {
        $this->repository = $repository;
        $this->resource   = $repository->find($id);
    }

    /**
     * @return string
     */
    protected function getActionUrl(): string
    {
        return '/admincp/music/genre/' . $this->resource->id;
    }

    /**
     * @return string
     */
    protected function getPluralizationItemType(): string
    {
        return __p('musics');
    }
}
