<?php

namespace MetaFox\Advertise\Notifications;

use MetaFox\Notification\Messages\MailMessage;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Notifications\ApproveNotification;

class MarkAsPaidNotification extends ApproveNotification
{
    protected string $type = 'advertise_mark_as_paid_notification';

    /**
     * Get the mail representation of the notification.
     *
     * @param $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $intro = __p('advertise::phrase.mark_ad_as_paid_message', [
            'title' => $this->getTitle(),
        ]);

        $url   = $this->model?->toUrl() ?? MetaFoxConstant::EMPTY_STRING;

        return (new MailMessage())->line($intro)
            ->action(__p('core::phrase.view_now'), $url);
    }

    public function callbackMessage(): ?string
    {
        return __p('advertise::phrase.mark_ad_as_paid_message', [
            'title' => $this->getTitle(),
        ]);
    }

    protected function getTitle(): string
    {
        return $this->model?->toTitle() ?? MetaFoxConstant::EMPTY_STRING;
    }
}
