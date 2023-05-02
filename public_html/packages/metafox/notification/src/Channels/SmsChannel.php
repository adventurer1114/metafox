<?php

namespace MetaFox\Notification\Channels;

use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Notifications\Notification;
use MetaFox\Sms\Contracts\ManagerInterface;
use MetaFox\Sms\Support\Message;

class SmsChannel
{
    /**
     * Create a new sms channel instance.
     *
     * @param  ManagerInterface $manager
     * @return void
     */
    public function __construct(protected ManagerInterface $manager)
    {
    }

    /**
     * Send the given notification.
     *
     * @param  IsNotifiable $notifiable
     * @param  Notification $notification
     * @return void
     */
    public function send(IsNotifiable $notifiable, Notification $notification)
    {
        $message = $notification->toTextMessage($notifiable);
        if (!$message instanceof Message) {
            return;
        }

        $recipients = $notifiable->routeNotificationFor('sms', $notification);
        if (empty($recipients)) {
            return;
        }

        $message->setRecipients($recipients);

        $this->manager->service()->send($message);
    }
}
