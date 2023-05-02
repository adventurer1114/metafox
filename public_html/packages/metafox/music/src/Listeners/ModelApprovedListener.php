<?php

namespace MetaFox\Music\Listeners;

use MetaFox\Music\Models\Album;
use MetaFox\Music\Models\Song;

class ModelApprovedListener
{
    /**
     * @param  mixed $model
     * @return void
     */
    public function handle($model): void
    {
        if (!$model instanceof Song) {
            return;
        }

        if (!$model->album instanceof Album) {
            return;
        }

        $model->album->incrementAmount('total_track');
        $model->album->incrementAmount('total_duration', $model->duration);
    }
}
