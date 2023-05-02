<?php

namespace MetaFox\Group\Notifications;

use MetaFox\Group\Models\Group as Model;
use MetaFox\Platform\Notifications\ApproveNotification;
use MetaFox\User\Support\Facades\UserEntity;

/**
 * Class AssignOwnerGroupNotification.
 *
 * @property Model $model
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 */
class AssignOwnerGroupNotification extends ApproveNotification
{
    protected string $type = 'assign_owner_notification';

    public function callbackMessage(): ?string
    {
        $owner = UserEntity::getById($this->model?->userId());

        if (!$owner) {
            return null;
        }

        if (!$this->user) {
            return null;
        }

        return $this->localize('group::notification.user_assign_owner_of_the_group_title_notification', [
            'user'  => $this->user->name,
            'owner' => $owner->name,
            'title' => $this->model->toTitle(),
        ]);
    }
}
