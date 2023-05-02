<?php

namespace MetaFox\Comment\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Comment\Jobs\DeleteCommentByItemJob;
use MetaFox\Platform\Contracts\Content;

class ModelDeletingListener
{
    /**
     * @param Model $model
     */
    public function handle(Model $model): void
    {
        if ($model instanceof Content) {
            DeleteCommentByItemJob::dispatch($model->entityId(), $model->entityType());
        }
    }
}
