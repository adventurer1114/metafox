<?php

namespace MetaFox\Group\Notifications;

use MetaFox\Group\Models\GroupChangePrivacy as Model;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;

/**
 * Class SuccessPrivacyNotification.
 *
 * @property Model $model
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 */
class SuccessPrivacyNotification extends Notification
{
    protected string $type = 'success_change_privacy';

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
        $title = $this->model->group->toTitle();

        $typeGroup = $this->model->privacy_type;

        return $this->localize('group::notification.notification_callback_message_when_success', [
            'title_group' => $title,
            'type_group'  => $this->localize(PrivacyTypeHandler::PRIVACY_PHRASE[$typeGroup]),
        ]);
    }

    public function toUrl(): ?string
    {
        $group = $this->model->group;

        if (null === $group) {
            return null;
        }

        return $group->toUrl();
    }

    public function toLink(): ?string
    {
        $group = $this->model->group;

        if (null === $group) {
            return null;
        }

        return $group->toLink();
    }
}
