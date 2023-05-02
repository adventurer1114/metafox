<?php

namespace MetaFox\Saved\Repositories\Eloquent;

use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Saved\Models\SavedSearchItem;
use MetaFox\Saved\Repositories\SavedSearchRepositoryInterface;

class SavedSearchRepository extends AbstractRepository implements SavedSearchRepositoryInterface
{
    public function model()
    {
        return SavedSearchItem::class;
    }

    public function createdBy(HasSavedItem $item): void
    {
        if (!$item instanceof Content) {
            return;
        }

        $data = $item->toSavedItem();

        if (!$data) {
            return;
        }

        $coreData = [
            'item_id'   => $item->entityId(),
            'item_type' => $item->entityType(),
            'title'     => $data['title'],
        ];

        $savedSearchItem = $this->getModel()->fill($coreData);
        $savedSearchItem->save();
    }

    public function updatedBy(HasSavedItem $item): void
    {
        if (!$item instanceof Content) {
            return;
        }

        $data = $item->toSavedItem();

        if (!$data) {
            return;
        }

        $coreData = [
            'title' => $data['title'],
        ];

        $this->getModel()->newQuery()
            ->where('item_id', '=', $item->entityId())
            ->where('item_type', '=', $item->entityType())
            ->update($coreData);
    }
}
