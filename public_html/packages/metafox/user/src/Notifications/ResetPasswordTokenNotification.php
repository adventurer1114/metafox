<?php

namespace MetaFox\User\Notifications;

use MetaFox\Notification\Messages\MailMessage;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Notifications\Notification;
use MetaFox\User\Models\PasswordResetToken;

/**
 * Class ResetPasswordNotification.
 */
class ResetPasswordTokenNotification extends Notification
{
    protected PasswordResetToken $token;

    protected array $channels = [];

    protected string $type = 'request_reset_password_token';

    protected string $as;

    public function __construct(PasswordResetToken $token, string $channel = 'mail', string $as = 'token')
    {
        parent::__construct();
        $this->channels = [$channel];
        $this->token    = $token;
        $this->as       = $as;
    }

    public function via(IsNotifiable $notifiable): array
    {
        return $this->channels;
    }

    public function toMail(): MailMessage
    {
        $service = new MailMessage();

        return match ($this->as) {
            'link'  => $this->sendPasswordUpdateForm($service, $this->token),
            default => $this->sendPasswordResetToken($service, $this->token),
        };
    }

    /**
     * @param  string                         $channel
     * @return ResetPasswordTokenNotification
     */
    public function channel(string $channel): self
    {
        if (in_array($channel, $this->channels)) {
            return $this;
        }

        $this->channels[] = $channel;

        return $this;
    }

    public function toArray(IsNotifiable $notifiable): array
    {
        return [];
    }

    public function callbackMessage(): ?string
    {
        return null; // @todo Sang
    }

    protected function sendPasswordUpdateForm(MailMessage $service, PasswordResetToken $token): MailMessage
    {
        $user = $token->userEntity;

        $subject = $this->localize('user::mail.password_request_subject', [
            'site_title' => Settings::get('core.general.site_name'),
        ]);

        $emailLine = $this->localize('user::mail.password_request_update_form_line', [
            'fullName' => $user ? $user->name : '',
            'link'     => $token->password_form_link,
        ]);

        return $service
            ->locale($this->getLocale())
            ->subject($subject)
            ->line($emailLine);
    }

    protected function sendPasswordResetToken(MailMessage $service, PasswordResetToken $token): MailMessage
    {
        $user = $token->userEntity;

        $subject = $this->localize('user::mail.password_request_subject', [
            'site_title' => Settings::get('core.general.site_name'),
        ]);
        $emailLine = $this->localize('user::mail.password_request_token_line', [
            'fullName' => $user ? $user->name : '',
            'token'    => $token->value,
        ]);

        return $service
            ->locale($this->getLocale())
            ->subject($subject)
            ->line($emailLine);
    }
}
