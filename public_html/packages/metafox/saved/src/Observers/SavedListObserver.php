<?php

namespace MetaFox\Saved\Observers;

use MetaFox\Saved\Models\SavedList;
use MetaFox\Saved\Models\SavedListData;

/**
 * Class SavedListObserver.
 */
class SavedListObserver
{
    public function deleted(SavedList $savedList): void
    {
        SavedListData::query()->where(['list_id' => $savedList->entityId()])->delete();
    }
}
