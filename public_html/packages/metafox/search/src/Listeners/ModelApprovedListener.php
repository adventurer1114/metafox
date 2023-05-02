<?php

namespace MetaFox\Search\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Search\Repositories\SearchRepositoryInterface;

class ModelApprovedListener
{
    public function handle(Model $model): void
    {
        if (!$model instanceof HasGlobalSearch) {
            return;
        }

        resolve(SearchRepositoryInterface::class)->createdBy($model);
    }
}
