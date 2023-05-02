<?php

namespace MetaFox\Photo\Observers;

use MetaFox\Photo\Models\CollectionStatistic;
use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Photo\Models\PhotoGroupItem;
use MetaFox\Platform\Contracts\HasApprove;

/**
 * Class PhotoGroupItemObserver.
 */
class PhotoGroupItemObserver
{
    private function increaseAmounts(PhotoGroup $group, string $itemType): void
    {
        $group->incrementAmount('total_item');

        if ($group->statistic instanceof CollectionStatistic) {
            $group->statistic->incrementTotalColumn($itemType);
        }
    }

    private function decreaseAmounts(PhotoGroup $group, string $itemType): void
    {
        if ($group->statistic instanceof CollectionStatistic) {
            $group->statistic->decrementTotalColumn($itemType);
        }

        $group->decrementAmount('total_item');
    }

    public function created(PhotoGroupItem $groupItem): void
    {
        if (!$groupItem->isApproved()) {
            return;
        }

        $this->increaseAmounts($groupItem->group, $groupItem->item_type);
    }

    public function deleted(PhotoGroupItem $groupItem): void
    {
        $this->decreaseAmounts($groupItem->group, $groupItem->item_type);
    }
}
