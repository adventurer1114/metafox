<?php

namespace MetaFox\User\Notifications;

use Illuminate\Bus\Queueable;
use MetaFox\Notification\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;
use MetaFox\User\Models\UserVerify;

/**
 * stub: packages/notifications/notification.stub.
 */

/**
 * Class VerifyEmailNotification.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 */
class VerifyEmail extends Notification
{
    use Queueable;

    protected string $type = 'user_verify_email_signup';

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed         $notifiable
     * @return array<string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed                                          $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        /** @var UserVerify $verification */
        $verification = app('user.verification')->generate($notifiable);

        $url = route('user.email.verify', [
            'hash' => $verification->hash_code,
        ], true);

        return (new MailMessage())
            ->locale($this->getLocale())
            ->subject(Lang::get('Verify Email Address'))
            ->line(Lang::get('Please click the button below to verify your email address.'))
            ->action(Lang::get('Verify Email Address'), $url)
            ->line(Lang::get('If you did not create an account, no further action is a required field.'));
    }

    public function toArray(IsNotifiable $notifiable): array
    {
        return [];
    }

    public function callbackMessage(): ?string
    {
        return null;
    }
}
