<?php

namespace MetaFox\User\Listeners;

use Illuminate\Support\Facades\Notification;
use MetaFox\Platform\Facades\Settings;
use MetaFox\User\Models\User;
use MetaFox\User\Notifications\WelcomeNewMember;

class UserVerifiedListener
{
    public function handle(User $user)
    {
        $this->handleWelcomeEmail($user);
    }

    private function handleWelcomeEmail(User $user)
    {
        if (!Settings::get('user.send_welcome_email')) {
            return;
        }

        if (!$user->hasVerifiedEmail()) {
            return;
        }

        Notification::send($user, new WelcomeNewMember($user));
    }
}
