<?php

namespace MetaFox\User\Notifications;

use MetaFox\Notification\Messages\MailMessage;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;
use MetaFox\User\Models\User;

/**
 * Class ProfileUpdatedByAdmin.
 * @property User $model
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ProfileUpdatedByAdmin extends Notification
{
    protected string $type = 'profile_updated_by_admin';

    /**
     * Get the mail representation of the notification.
     *
     * @param IsNotifiable $notifiable
     *
     * @return MailMessage
     */
    public function toMail(IsNotifiable $notifiable): MailMessage
    {
        $service = new MailMessage();

        return $service
            ->locale($this->getLocale())
            ->subject($this->localize('user::phrase.mail.profile_updated_by_admin_subject'))
            ->line($this->localize('user::phrase.mail.profile_updated_by_admin_text'))
            ->action($this->localize('core::phrase.view_now'), $this->model->toUrl());
    }

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
        return $this->localize('user::notification.profile_updated_by_admin');
    }
}
