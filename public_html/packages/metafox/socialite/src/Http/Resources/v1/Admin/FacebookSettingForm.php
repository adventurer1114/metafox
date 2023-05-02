<?php

namespace MetaFox\Socialite\Http\Resources\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use MetaFox\Form\AdminSettingForm as Form;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;

/**
 * | --------------------------------------------------------------------------
 * | Form Configuration
 * | --------------------------------------------------------------------------
 * | stub: src/Http/Resources/v1/Admin/SiteSettingForm.stub.
 */

/**
 * Class FacebookSettingForm.
 * @codeCoverageIgnore
 * @driverType site-settings
 */
class FacebookSettingForm extends Form
{
    /**
     * @var string
     */
    private $namespace = 'core.services.facebook';

    protected function prepare(): void
    {
        $vars   = [
            'client_id',
            'client_secret',
            'redirect',
            'login_enabled',
        ];

        $value = [];
        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get("$this->namespace.$var"));
        }

        $this->title(__p('socialite::facebook.site_settings'))
            ->action(url_utility()->makeApiUrl('admincp/setting/socialite.facebook'))
            ->asPost()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('client_id')
                    ->label(__p('socialite::facebook.client_id'))
                    ->required(),
                Builder::text('client_secret')
                    ->label(__p('socialite::facebook.client_secret'))
                    ->required(),
                Builder::text('redirect')
                    ->label(__p('socialite::facebook.redirect_url'))
                    ->required(),
                Builder::checkbox('login_enabled')
                    ->label(__p('socialite::facebook.login_enabled'))
            );

        $this->addDefaultFooter(true);
    }

    /**
     * @param  Request      $request
     * @return array<mixed>
     */
    public function validated(Request $request): array
    {
        return [
            $this->namespace => $request->validate([
                'client_id'     => 'required|string',
                'client_secret' => 'required|string',
                'redirect'      => 'required|string',
                'login_enabled' => 'sometimes|nullable|numeric',
            ]),
        ];
    }
}
