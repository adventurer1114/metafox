<?php

namespace MetaFox\Marketplace\Notifications;

use MetaFox\Notification\Messages\MailMessage;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;

class ExpiredNotification extends Notification
{
    /**
     * @var string
     */
    protected string $type = 'listing_expired_notification';

    /**
     * @var int
     */
    protected int $expiredDays = 0;

    /**
     * @param  int   $days
     * @return $this
     */
    public function setExpiredDays(int $days): self
    {
        $this->expiredDays = $days;

        return $this;
    }

    public function toArray(IsNotifiable $notifiable): array
    {
        return [];
    }

    public function callbackMessage(): ?string
    {
        return null;
    }

    public function toMail(): ?MailMessage
    {
        $service = new MailMessage();

        if (null === $this->model) {
            return null;
        }

        if (0 == $this->expiredDays) {
            return null;
        }

        $subject = $this->localize('marketplace::phrase.expired_email_subject', [
            'title' => $this->model->toTitle(),
        ]);

        $message = $this->localize('marketplace::phrase.expired_email_message', [
            'title' => $this->model->toTitle(),
            'days'  => $this->expiredDays,
        ]);

        return $service
            ->locale($this->getLocale())
            ->subject($subject)
            ->line($message)
            ->action($this->localize('core::phrase.view_now'), $this->model->toUrl());
    }
}
