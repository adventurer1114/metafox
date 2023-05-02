<?php

namespace MetaFox\Page\Notifications;

use MetaFox\Page\Models\Page;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;

class ApproveNewPostNotification extends Notification
{
    protected string $type = 'page_new_post';

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
        $user  = $this->model->user;

        if (null === $owner) {
            return __p('page::notification.page_new_post_in_a_page_message', [
                'user_full_name' => $user->full_name,
            ]);
        }

        if ($user instanceof Page) {
            return $this->localize('page::notification.page_new_post_in_page_message_post_as_page', [
                'user_full_name' => $user->toTitle(),
            ]);
        }

        return __p('page::notification.page_new_post_in_page_message', [
            'user_full_name' => $user->full_name,
            'title'          => $owner->toTitle(),
        ]);
    }
}
