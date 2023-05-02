<?php

namespace MetaFox\Page\Observers;

use Illuminate\Support\Carbon;
use MetaFox\Page\Models\PageInvite as Model;

/**
 * Class PageInviteObserver.
 */
class PageInviteObserver
{
    public function creating(Model $invite): void
    {
        $invite->expired_at = Carbon::now()->addDays(Model::EXPIRE_DAY);
    }

    public function updating(Model $invite): void
    {
        if ($invite->status_id == Model::STATUS_PENDING) {
            $invite->expired_at = Carbon::now()->addDays(Model::EXPIRE_DAY);
        }
    }
}
