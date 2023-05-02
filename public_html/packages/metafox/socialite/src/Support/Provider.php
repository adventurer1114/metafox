<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Socialite\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MetaFox\Form\AbstractField;
use MetaFox\Form\Builder;
use MetaFox\Form\Mobile\Builder as MobileBuilder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;

/**
 * Class Provider.
 */
class Provider
{
    public const FACEBOOK = 'facebook';
    public const GOOGLE   = 'google';
    public const APPLE    = 'apple';

    /**
     * @return array<mixed>
     */
    private function fieldMaps(): array
    {
        // TODO: dynamically load this
        return [
            MetaFoxConstant::RESOLUTION_MOBILE => [
                self::FACEBOOK => MobileBuilder::facebookLoginButton(),
                self::GOOGLE   => MobileBuilder::googleLoginButton(),
                self::APPLE    => MobileBuilder::appleLoginButton(),
            ],
            MetaFoxConstant::RESOLUTION_WEB => [
                self::FACEBOOK => Builder::facebookLoginButton(),
                self::GOOGLE   => Builder::googleLoginButton(),
            ],
        ];
    }

    /**
     * @return array<string>
     */
    public function getEnabledProviders(): array
    {
        $providers = [];
        foreach (Settings::get('core.services') as $provider => $config) {
            if (!Arr::get($config, 'login_enabled')) {
                continue;
            }

            $providers[] = $provider;
        }

        return $providers;
    }

    /**
     * @param string $provider
     * @param string $resolution
     *
     * @return ?AbstractField
     */
    public function buildFormField(string $provider, string $resolution = 'web'): ?AbstractField
    {
        $maps = $this->fieldMaps();

        return Arr::get($maps, "$resolution.$provider");
    }

    /**
     * @param string $resolution
     *
     * @return array<AbstractField>
     */
    public function buildFormFields(string $resolution = 'web'): array
    {
        $fields = [];
        foreach ($this->getEnabledProviders() as $provider) {
            $field = $this->buildFormField($provider, $resolution);
            if (empty($field)) {
                continue;
            }

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * @return array<mixed>
     */
    public function getProviderSettings(): array
    {
        $settings  = [];
        $providers = $this->getEnabledProviders();
        foreach ($providers as $provider) {
            $settings[$provider] = array_filter(Settings::get("core.services.{$provider}", []), function ($key) {
                return !Str::contains($key, ['secret', 'private'], true);
            }, ARRAY_FILTER_USE_KEY);
        }

        return $settings;
    }
}
