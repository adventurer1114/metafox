<?php

namespace MetaFox\Photo\Listeners;

use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Platform\Contracts\HasAmounts;
use MetaFox\Platform\Contracts\HasTotalComment;

class UpdateTotalCommentListener
{
    public function handle(HasAmounts $model): void
    {
        if (!$model instanceof PhotoGroup) {
            return;
        }

        if ($model->total_item != 1) {
            return;
        }

        foreach ($model->items as $item) {
            if ($item?->detail instanceof HasTotalComment) {
                $item->detail->update(['total_comment' => $model->total_comment]);
            }
        }
    }
}
