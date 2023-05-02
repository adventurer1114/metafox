<?php

namespace MetaFox\Saved\Http\Resources\v1\SavedList;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Saved\Models\SavedList as Model;
use MetaFox\Saved\Repositories\SavedListRepositoryInterface;
use MetaFox\Saved\Repositories\SavedRepositoryInterface;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class AddToCollectionMobileForm.
 * @property ?Model $resource
 */
class AddToCollectionMobileForm extends AbstractForm
{
    /** @var bool */
    protected $isEdit = false;
    protected int $itemId;

    protected function prepare(): void
    {
        $saved         = $this->savedRepository()->getCollectionByItem($this->itemId);
        $collectionIds = $saved->savedLists->pluck('pivot.list_id')->unique()->toArray();
        $this->title(__p('saved::phrase.new_collection'))
            ->action(url_utility()->makeApiUrl('saveditems/collection'))
            ->asPut()
            ->setValue([
                'item_id'        => $this->itemId,
                'collection_ids' => !empty($collectionIds) ? $collectionIds : [],
            ]);
    }

    public function boot(?int $id): void
    {
        $this->itemId = $id;
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::clickable()
                ->label(__p('saved::phrase.create_new_collection'))
                ->params(['item_id' => $this->itemId])
                ->action('addItemToNewCollection'),
            Builder::choice('collection_ids')
                ->multiple()
                ->label(__p('core::phrase.name'))
                ->options($this->getOptions()),
            Builder::hidden('item_id')
        );
    }

    protected function getOptions(): array
    {
        $saveLists = $this->repository()->getSavedListByUser(user());
        $options   = [];
        foreach ($saveLists as $item) {
            /* @var Model $item */
            $options[] = [
                'label' => $item->name,
                'value' => $item->entityId(),
            ];
        }

        return $options;
    }

    protected function repository(): SavedListRepositoryInterface
    {
        return resolve(SavedListRepositoryInterface::class);
    }

    protected function savedRepository(): SavedRepositoryInterface
    {
        return resolve(SavedRepositoryInterface::class);
    }
}
