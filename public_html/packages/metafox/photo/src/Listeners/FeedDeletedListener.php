<?php

namespace MetaFox\Photo\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Photo\Models\PhotoGroup;

class FeedDeletedListener
{
    public function handle(Model $model): void
    {
        if ($model instanceof PhotoGroup) {
            app('events')->dispatch('comment.delete_by_item', [$model]);
            app('events')->dispatch('like.delete_by_item', [$model]);
        }
    }

}
