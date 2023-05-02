<?php

namespace MetaFox\Comment\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Comment\Jobs\DeleteCommentByItemJob;
use MetaFox\Platform\Contracts\Entity;

class ModelDeletingListener
{
    /**
     * @param Model $model
     */
    public function handle(Model $model): void
    {
        if ($model instanceof Entity) {
            DeleteCommentByItemJob::dispatch($model->entityId(), $model->entityType());
        }
    }
}
