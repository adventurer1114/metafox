<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Listeners;

use MetaFox\Platform\Contracts\HasShortcutItem;
use MetaFox\Platform\Contracts\User;
use MetaFox\User\Repositories\UserPrivacyRepositoryInterface;
use MetaFox\User\Repositories\UserShortcutRepositoryInterface;
use MetaFox\User\Support\Facades\UserEntity;

class ModelDeletedListener
{
    public function userPrivacyRepository()
    {
        return resolve(UserPrivacyRepositoryInterface::class);
    }

    public function handle($model)
    {
        if ($model instanceof User) {
            UserEntity::deleteEntity($model->entityId());

            if ($model->entityType() == \MetaFox\User\Models\User::ENTITY_TYPE) {
                $this->userPrivacyRepository()->deleteUserPrivacy($model->entityId());
            }
            $this->handleShortcutByItem($model);
        }
        $this->handleShortcut($model);
    }

    protected function handleShortcut($model)
    {
        if ($model instanceof HasShortcutItem) {
            resolve(UserShortcutRepositoryInterface::class)->deletedBy($model);
        }
    }

    protected function handleShortcutByItem($model)
    {
        resolve(UserShortcutRepositoryInterface::class)->deletedByItem($model);
    }
}
