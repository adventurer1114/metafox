<?php

namespace MetaFox\Forum\Notifications;

use MetaFox\Notification\Messages\MailMessage;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;

class CopyThread extends Notification
{
    protected string $type = 'copy_thread';

    /**
     * @param  IsNotifiable  $notifiable
     * @return array|mixed[]
     */
    public function toArray(IsNotifiable $notifiable): array
    {
        $model = $this->model;

        if (null === $model) {
            return [];
        }

        return [
            'data'      => $model->toArray(),
            'item_id'   => $model->entityId(),
            'item_type' => $model->entityType(),
            'user_id'   => $model->userId(),
            'user_type' => $model->userType(),
        ];
    }

    public function toMail(): ?MailMessage
    {
        $model = $this->model;

        $hasModel = null !== $model;

        switch ($hasModel) {
            case true:
                $subject = $this->localize('forum::phrase.your_thread_copying_process_has_been_success_subject');
                $content = $this->localize('forum::phrase.your_thread_copying_process_has_been_success_content');
                break;
            default:
                $subject = $this->localize('forum::phrase.your_thread_copying_process_has_been_failed_subject');
                $content = $this->localize('forum::phrase.your_thread_copying_process_has_been_failed_content');
                break;
        }

        $service = new MailMessage();

        $service = $service
            ->locale($this->getLocale())
            ->subject($subject)
            ->line($content);

        if ($hasModel) {
            $service->action($this->localize('core::phrase.review_now'), $this->toUrl());
        }

        return $service;
    }

    public function callbackMessage(): ?string
    {
        $model = $this->model;

        if (null === $model) {
            return $this->localize('forum::notification.your_thread_copying_process_has_been_failed');
        }

        return $this->localize('forum::notification.your_thread_copying_process_has_been_success_review_now');
    }

    public function toUrl(): ?string
    {
        return $this->model->toUrl();
    }

    public function toLink(): ?string
    {
        return $this->model->toLink();
    }
}
