<?php

namespace MetaFox\Photo\Observers;

use MetaFox\Photo\Models\CollectionStatistic;
use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Photo\Models\PhotoGroupItem;

/**
 * Class PhotoGroupItemObserver.
 */
class PhotoGroupItemObserver
{
    private function increaseAmounts(?PhotoGroup $group, string $itemType): void
    {
        if (!$group) {
            return;
        }
        $group->incrementAmount('total_item');

        if ($group->statistic instanceof CollectionStatistic) {
            $group->statistic->incrementTotalColumn($itemType);
        }
    }

    private function decreaseAmounts(?PhotoGroup $group, string $itemType): void
    {
        if (!$group) {
            return;
        }

        if ($group->statistic instanceof CollectionStatistic) {
            $group->statistic->decrementTotalColumn($itemType);
        }

        $group->decrementAmount('total_item');
    }

    public function creating(?PhotoGroupItem $groupItem): void
    {
        if (!$groupItem) {
            return;
        }
        if (!$groupItem->isApproved()) {
            return;
        }

        $photoGroup = $groupItem->group;

        if ($photoGroup->total_item > 1) {
            return;
        }

        $item = $photoGroup->items()->first();

        if ($item instanceof PhotoGroupItem) {
            $item->detail->update(['total_comment' => 0]);
        }
    }

    public function created(?PhotoGroupItem $groupItem): void
    {
        if (!$groupItem) {
            return;
        }

        if (!$groupItem->isApproved()) {
            return;
        }

        $photoGroup = $groupItem->group;
        $this->increaseAmounts($photoGroup, $groupItem->item_type);
    }

    protected function handleUpdateTotalCommentOnlyItem(PhotoGroupItem $groupItem, PhotoGroup $photoGroup): void
    {
        $groupItem->detail->update(['total_comment' => $photoGroup->total_comment]);
        app('events')->dispatch('comment.delete_by_item', [$groupItem->detail]);
    }

    public function deleted(?PhotoGroupItem $groupItem): void
    {
        if (!$groupItem) {
            return;
        }

        $photoGroup = $groupItem->group;
        $this->decreaseAmounts($photoGroup, $groupItem->item_type);

        $photoGroup->refresh();
        if ($photoGroup->total_item > 1) {
            return;
        }

        $item = $photoGroup->items()->first();
        if ($item instanceof PhotoGroupItem) {
            $this->handleUpdateTotalCommentOnlyItem($item, $photoGroup);
        }
    }
}
