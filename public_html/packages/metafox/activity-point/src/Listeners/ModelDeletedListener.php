<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\ActivityPoint\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\ActivityPoint\Support\ActivityPoint as PointSupport;
use MetaFox\ActivityPoint\Support\Facade\ActivityPoint;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;

/**
 * Class ModelDeletedListener.
 * @ignore
 */
class ModelDeletedListener
{
    /**
     * @param Model $model
     */
    public function handle($model): void
    {
        if ($model instanceof Content) {
            $this->handleRetrieveActivityPoint($model);
        }
    }

    /**
     * @param Content $model
     */
    private function handleRetrieveActivityPoint(Content $model): void
    {
        if (!$model instanceof Model) {
            return;
        }

        if ($model->isDraft() || !$model->isApproved()) {
            return;
        }

        $user = $model->user;
        if (!$user instanceof User) {
            return;
        }

        ActivityPoint::updateUserPoints($user, $model, 'create', PointSupport::TYPE_RETRIEVED);
    }
}
