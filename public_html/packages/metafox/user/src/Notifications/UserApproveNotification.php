<?php

namespace MetaFox\User\Notifications;

use MetaFox\Notification\Messages\MailMessage;
use MetaFox\Platform\Notifications\ApproveNotification;
use MetaFox\User\Models\User as Model;

/**
 * Class UserApproveNotification.
 * @property Model $model
 * @ignore
 */
class UserApproveNotification extends ApproveNotification
{
    protected string $type = 'user_approve_notification';

    /**
     * Get the mail representation of the notification.
     *
     * @return MailMessage
     */
    public function toMail(): MailMessage
    {
        $url = $this->model->toUrl();

        return (new MailMessage())
            ->locale($this->getLocale())
            ->subject($this->localize('user::phrase.user_approved_notification_type'))
            ->line($this->localize('user::phrase.your_account_has_been_approved'))
            ->action($this->localize('user::phrase.user'), $url);
    }

    public function callbackMessage(): ?string
    {
        return $this->localize('user::phrase.user_has_been_approved');
    }
}
