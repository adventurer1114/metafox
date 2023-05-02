<?php

namespace MetaFox\Search\Observers;

use MetaFox\Search\Models\Search;

/**
 * Class SearchObserver.
 * @ignore
 * @codeCoverageIgnore
 */
class SearchObserver
{
    public function creating(Search $model)
    {
        if ($model->title === null) {
            $model->title = '';
        }

        if ($model->text === null) {
            $model->text = '';
        }
    }

    public function updating(Search $model)
    {
        if ($model->title === null) {
            $model->title = '';
        }

        if ($model->text === null) {
            $model->text = '';
        }
    }

    public function deleted(Search $model): void
    {
        $model->privacyStreams()->delete();

        $model->tagData()->detach();
    }
}
