<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Listeners;

use MetaFox\Platform\Contracts\User;
use MetaFox\User\Support\Facades\UserEntity;

class ModelUpdatedListener
{
    public function handle($model)
    {
        if ($model instanceof User) {
            UserEntity::updateEntity($model->entityId(), $model->toUserResource());
        }
    }
}
