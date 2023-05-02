<?php

namespace MetaFox\Search\Listeners;

use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Search\Repositories\SearchRepositoryInterface;

/**
 * Class ModelUpdatedListener.
 * @ignore
 * @codeCoverageIgnore
 */
class ModelUpdatedListener
{
    /**
     * @param mixed $model
     */
    public function handle($model): void
    {
        if ($model instanceof HasGlobalSearch) {
            resolve(SearchRepositoryInterface::class)->updatedBy($model);
        }
    }
}
