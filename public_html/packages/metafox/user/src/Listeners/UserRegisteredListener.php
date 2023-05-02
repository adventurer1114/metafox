<?php

namespace MetaFox\User\Listeners;

use MetaFox\Platform\Facades\Settings;
use MetaFox\User\Models\User;
use MetaFox\User\Repositories\UserPrivacyRepositoryInterface;
use MetaFox\User\Support\Facades\UserValue;
use MetaFox\User\Support\User as UserSupport;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class UserRegisteredListener
{
    public function __construct(protected UserPrivacyRepositoryInterface $userPrivacyRepository)
    {
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function handle(User $user)
    {
        $this->handleUserVerification($user);
        $this->handleBirthdayPrivacy($user);

        $privacyDefault = Settings::get('user.on_register_privacy_setting');
        $this->userPrivacyRepository->updateUserPrivacy($user->entityId(), ['profile:view_profile' => $privacyDefault]);
    }

    private function handleUserVerification(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        if (!Settings::get('user.verify_email_at_signup')) {
            $user->markAsVerified();

            return;
        }

        if (empty($user->email)) {
            // user who registered using phone number or socialite (without email)
            // should be marked as verified by default
            $user->markAsVerified();

            return;
        }

        app('user.verification')->send($user);
    }

    private function handleBirthdayPrivacy(User $user): void
    {
        $defaultBirthdayPrivacy = Settings::get('user.default_birthday_privacy', UserSupport::DATE_OF_BIRTH_SHOW_ALL);

        UserValue::updateUserValueSetting($user, ['user_profile_date_of_birth_format' => $defaultBirthdayPrivacy]);
    }
}
