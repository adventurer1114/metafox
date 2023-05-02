<?php

namespace MetaFox\Subscription\Notifications;

use MetaFox\Notification\Messages\MailMessage;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;

class DeletePackage extends Notification
{
    protected string $type = 'subscription_delete_package';

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
        if (null === $this->model) {
            return null;
        }

        return $this->getMessage();
    }

    public function toMail(): ?MailMessage
    {
        $service = new MailMessage();

        $subject = $this->getMailSubject();

        $message = $this->getMessage();

        return $service
            ->locale($this->getLocale())
            ->subject($subject)
            ->line($message);
    }

    public function toUrl(): ?string
    {
        return null;
    }

    public function toLink(): ?string
    {
        return null;
    }

    protected function getMessage(): ?string
    {
        if (null === $this->model) {
            return null;
        }

        return $this->localize('subscription::phrase.the_package_title_is_no_longer_available', [
            'title' => $this->model->toTitle(),
        ]);
    }

    protected function getMailSubject(): string
    {
        return $this->localize('subscription::phrase.the_package_title_is_no_longer_available_subject', [
            'title' => $this->model?->toTitle(),
        ]);
    }
}
