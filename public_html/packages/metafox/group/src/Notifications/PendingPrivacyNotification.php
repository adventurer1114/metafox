<?php

namespace MetaFox\Group\Notifications;

use Illuminate\Support\Carbon;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Models\GroupChangePrivacy as Model;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;

/**
 * Class PendingPrivacyNotification.
 *
 * @property Model $model
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 */
class PendingPrivacyNotification extends Notification
{
    protected string $type = 'pending_privacy';

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
        $title          = $this->model->group->toTitle();
        $typeGroup      = $this->model->group->getPrivacyType();
        $type_group_new = $this->model->privacy_type;
        $numberDays     = Carbon::parse($this->model->expired_at)->diffInDays($this->model->created_at);

        if ($numberDays == 0) {
            return $this->localize('group::notification.notification_callback_message_when_pending_not_wait', [
                'title_group'    => $title,
                'type_group'     => $this->localize(PrivacyTypeHandler::PRIVACY_PHRASE[$typeGroup]),
                'type_group_new' => $this->localize(PrivacyTypeHandler::PRIVACY_PHRASE[$type_group_new]),
            ]);
        }

        return $this->localize('group::notification.notification_callback_message_when_pending', [
            'title_group'    => $title,
            'type_group'     => $this->localize(PrivacyTypeHandler::PRIVACY_PHRASE[$typeGroup]),
            'type_group_new' => $this->localize(PrivacyTypeHandler::PRIVACY_PHRASE[$type_group_new]),
            'number_setting' => $numberDays,
        ]);
    }

    public function toLink(): ?string
    {
        if (null === $this->model) {
            return null;
        }

        $group = $this->model->group;

        if ($group instanceof Group) {
            return $group->toLink();
        }

        return null;
    }

    public function toUrl(): ?string
    {
        if (null === $this->model) {
            return null;
        }

        $group = $this->model->group;

        if ($group instanceof Group) {
            return $group->toUrl();
        }

        return null;
    }

    public function toRouter(): ?string
    {
        $group = $this->model?->group;

        return $group instanceof Group ? $group->toRouter() : null;
    }
}
