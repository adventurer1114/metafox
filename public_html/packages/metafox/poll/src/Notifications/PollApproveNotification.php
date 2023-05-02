<?php

namespace MetaFox\Poll\Notifications;

use MetaFox\Notification\Messages\MailMessage;
use MetaFox\Platform\Notifications\ApproveNotification;
use MetaFox\Poll\Models\Poll as Model;

/**
 * stub: packages/notifications/notification.stub.
 */

/**
 * Class PollApproveNotification.
 * @property Model $model
 * @ignore
 */
class PollApproveNotification extends ApproveNotification
{
    protected string $type = 'poll_approve_notification';

    /**
     * Get the mail representation of the notification.
     *
     * @param $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $intro = $this->localize('poll::phrase.poll_approved_successfully_notification');
        $url   = $this->model->toUrl();

        return (new MailMessage())
            ->locale($this->getLocale())
            ->line($intro)
            ->action($this->localize('poll::phrase.poll'), $url);
    }

    public function callbackMessage(): ?string
    {
        return $this->localize('poll::phrase.poll_approved_successfully_notification');
    }
}
