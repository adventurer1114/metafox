<?php

namespace MetaFox\Photo\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\Media;
use MetaFox\Photo\Repositories\PhotoGroupRepositoryInterface;

class ModelApprovedListener
{
    /**
     * @param  mixed $model
     * @return void
     */
    public function handle($model): void
    {
        if ($model instanceof Media) {
            $this->handleUpdateMedia($model);
        }
    }

    private function handleUpdateMedia(Media $model): void
    {
        if (!$model instanceof Model) {
            return;
        }

        $this->updatePhotoGroupStatus($model);
    }

    private function updatePhotoGroupStatus(Media $model): void
    {
        if (!$model->group_id) {
            return;
        }

        resolve(PhotoGroupRepositoryInterface::class)->updateApprovedStatus($model->group_id);
    }
}
