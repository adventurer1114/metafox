<?php

namespace MetaFox\Group\Notifications;

use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;

class ApproveNewPostNotification extends Notification
{
    protected string $type = 'approved_new_post';

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
        $owner = $this->model->owner;

        if (null === $owner) {
            return $this->localize('group::notification.approved_new_post_in_a_group_message', [
                'user_full_name' => $this->model->user->full_name,
            ]);
        }

        return $this->localize('group::notification.approved_new_post_in_group_message', [
            'user_full_name' => $this->model->user->full_name,
            'title'          => $this->model->owner->toTitle(),
        ]);
    }
}
