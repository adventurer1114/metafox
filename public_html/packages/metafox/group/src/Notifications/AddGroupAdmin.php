<?php

namespace MetaFox\Group\Notifications;

use MetaFox\Group\Models\Invite;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;

class AddGroupAdmin extends Notification
{
    protected string $type = 'add_group_admin';

    /**
     * @inheritDoc
     */
    public function toArray(IsNotifiable $notifiable): array
    {
        return [
            'data'      => $this->model->toArray(),
            'item_id'   => $this->model->entityId(),
            'item_type' => $this->model->entityType(),
            'user_id'   => $this->model->userId(),
            'user_type' => $this->model->userType(),
        ];
    }

    public function callbackMessage(): ?string
    {
        $model = $this->model;
        if (!$model instanceof Invite) {
            return null;
        }

        $userEntity   = $this->model->userEntity;
        $userFullName = $userEntity->name;
        $groupTitle   = $model->group->name;

        return $this->localize('group::notification.user_full_name_invited_you_to_became_an_admin_for_the_group_title', [
            'user_full_name' => $userFullName,
            'title'          => $groupTitle,
        ]);
    }
}
