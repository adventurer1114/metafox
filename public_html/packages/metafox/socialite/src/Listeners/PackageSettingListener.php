<?php

namespace MetaFox\Socialite\Listeners;

use MetaFox\Platform\Support\BasePackageSettingListener;

/**
 * Class PackageSettingListener.
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getEvents(): array
    {
        return [
            'socialite.social_account.callback' => [
                SocialAccountCallbackListener::class,
            ],
            'socialite.social_account.request' => [
                SocialAccountRequestListener::class,
            ],
            'socialite.login_fields' => [
                SocialLoginFieldsListener::class,
            ],
            'socialite.provider.config' => [
                AppleProviderConfigListener::class,
            ],
            \SocialiteProviders\Manager\SocialiteWasCalled::class => [
                // ... other providers
                \SocialiteProviders\Apple\AppleExtendSocialite::class . '@handle',
            ],
        ];
    }
}
