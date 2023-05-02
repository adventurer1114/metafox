<?php

namespace MetaFox\Saved\Http\Resources\v1\SavedList;

use MetaFox\Form\Mobile\Builder;
use MetaFox\Saved\Models\SavedList as Model;
use MetaFox\Saved\Repositories\SavedListRepositoryInterface;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateSavedListMobileForm.
 * @property Model $resource
 */
class UpdateSavedListMobileForm extends StoreSavedListForm
{
    /** @var bool */
    protected $isEdit = false;

    public function boot(SavedListRepositoryInterface $repository, ?int $id): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $name = null;
        if ($this->resource instanceof Model) {
            $name = $this->resource->name;
        }

        $this->title(__('saved::phrase.edit_collection'))
            ->action(url_utility()->makeApiUrl("saveditems-collection/{$this->resource->entityId()}"))
            ->asPut()
            ->setValue([
                'name'    => $name,
                'privacy' => $this->resource->privacy,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::text('name')
                ->required()
                ->label(__p('core::phrase.name'))
                ->placeholder(
                    __p(
                        'core::phrase.maximum_length_of_characters',
                        ['length' => Model::MAXIMUM_NAME_LENGTH]
                    )
                ),
            Builder::privacy()
        );
    }
}
