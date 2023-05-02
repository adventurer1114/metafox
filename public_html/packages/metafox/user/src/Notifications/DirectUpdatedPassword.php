<?php

namespace MetaFox\User\Notifications;

use App\Models\User;
use MetaFox\Notification\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;

/**
 * Class DirectUpdatedPassword.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DirectUpdatedPassword extends Notification
{
    protected string $type = 'new_password_updated';
    protected string $password;

    public function __construct(string $password, $model = User::class)
    {
        parent::__construct($model);
        $this->password = $password;
    }

    public function toMail(): MailMessage
    {
        $service = new MailMessage();

        Log::channel('dev')->info('send mail message', [$this->password]);

        //todo: need format email template
        return $service
            ->locale($this->getLocale())
            ->line('Your new password is ' . $this->password)
            ->line('Thank you for using our application!');
    }

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
        return null; // @todo Sang
    }

    public function toLink(): ?string
    {
        return null;
    }

    public function toUrl(): ?string
    {
        return null;
    }

    public function toRouter(): ?string
    {
        return null;
    }
}
