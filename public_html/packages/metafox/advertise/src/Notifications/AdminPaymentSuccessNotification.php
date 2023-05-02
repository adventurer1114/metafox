<?php

namespace MetaFox\Advertise\Notifications;

use MetaFox\Notification\Messages\MailMessage;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Notifications\ApproveNotification;

class AdminPaymentSuccessNotification extends ApproveNotification
{
    protected string $type = 'advertise_payment_success_ad_notification';

    /**
     * Get the mail representation of the notification.
     *
     * @param $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $intro = __p('advertise::phrase.an_admin_has_paid_your_ad_title', [
            'title' => $this->getTitle(),
        ]);

        $url   = $this->model?->toUrl() ?? MetaFoxConstant::EMPTY_STRING;

        return (new MailMessage())->line($intro)
            ->action(__p('core::phrase.view_now'), $url);
    }

    public function callbackMessage(): ?string
    {
        return __p('advertise::phrase.an_admin_has_paid_your_ad_title', [
            'title' => $this->getTitle(),
        ]);
    }

    protected function getTitle(): string
    {
        return $this->model?->toTitle() ?? MetaFoxConstant::EMPTY_STRING;
    }
}
