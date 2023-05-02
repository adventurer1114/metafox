<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Saved\Listeners;

use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Contracts\User;
use MetaFox\Saved\Repositories\SavedAggRepositoryInterface;
use MetaFox\Saved\Repositories\SavedListRepositoryInterface;
use MetaFox\Saved\Repositories\SavedRepositoryInterface;

/**
 * Class ModelDeletedListener.
 * @ignore
 * @codeCoverageIgnore
 */
class ModelDeletedListener
{
    /**
     * @param mixed $model
     *
     * @return void
     */
    public function handle($model)
    {
        if ($model instanceof User) {
            app(SavedListRepositoryInterface::class)->deleteForUser($model);
            app(SavedRepositoryInterface::class)->deleteForUser($model);
            app(SavedAggRepositoryInterface::class)->deleteForUser($model);
        }

        if ($model instanceof HasSavedItem) {
            app(SavedRepositoryInterface::class)->deleteForItem($model);
        }
    }
}
