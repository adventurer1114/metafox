<?php

namespace MetaFox\Activity\Observers;

use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Share;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasTotalShare;

/**
 * Class ShareObserver.
 */
class ShareObserver
{
    public function created(Share $share): void
    {
        $item = $share->item;

        if ($item instanceof Feed) {
            if ($item->item instanceof HasTotalShare) {
                $item->item->incrementAmount('total_share');
            }
        }

        if ($item instanceof HasTotalShare) {
            $item->incrementAmount('total_share');
        }

        $this->redundantFeed($item);
    }

    public function deleted(Share $share): void
    {
        $item = $share->item;

        if ($item instanceof Feed) {
            if ($item->item instanceof HasTotalShare) {
                $item->item->decrementAmount('total_share');
            }
        }

        if ($item instanceof HasTotalShare) {
            $item->decrementAmount('total_share');
        }

        $this->redundantFeed($item);
    }

    private function redundantFeed(?Entity $item): void
    {
        app('events')->dispatch('activity.redundant', [$item], true);
    }
}
