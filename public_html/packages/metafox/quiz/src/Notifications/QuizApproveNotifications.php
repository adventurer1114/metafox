<?php

namespace MetaFox\Quiz\Notifications;

use MetaFox\Notification\Messages\MailMessage;
use MetaFox\Platform\Notifications\ApproveNotification;
use MetaFox\Quiz\Models\Quiz as Model;

/**
 * stub: packages/notifications/notification.stub.
 */

/**
 * Class QuizApproveNotifications.
 * @property Model $model
 * @ignore
 */
class QuizApproveNotifications extends ApproveNotification
{
    protected string $type = 'quiz_approve_notification';

    /**
     * Get the mail representation of the notification.
     *
     * @param $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $intro = $this->localize('quiz::notification.quiz_approved_successfully_notification');
        $url   = $this->model->toUrl();

        return (new MailMessage())
            ->locale($this->getLocale())
            ->line($intro)
            ->action($this->localize('quiz::phrase.quiz'), $url);
    }

    public function callbackMessage(): ?string
    {
        return $this->localize('quiz::notification.quiz_approved_successfully_notification');
    }
}
