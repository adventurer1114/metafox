<?php

namespace MetaFox\Video\Http\Resources\v1\Admin;

use Illuminate\Support\Arr;
use MetaFox\Form\AdminSettingForm as Form;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Video\Models\Video as Model;
use MetaFox\Video\Support\Providers\Mux;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SiteSettingForm.
 * @property ?Model $resource
 */
class MuxSettingForm extends Form
{
    protected function prepare(): void
    {
        $vars   = [
            'video.mux.client_id',
            'video.mux.client_secret',
            'video.mux.webhook_secret',
        ];

        $value = [];

        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get($var));
        }

        $this->title(__p('video::phrase.mux_configurations'))
            ->asPost()
            ->action(url_utility()->makeApiUrl('admincp/setting/video.mux'))
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('video.mux.client_id')
                    ->required()
                    ->label(__p('video::phrase.mux_client_id'))
                    ->description(__p('video::phrase.mux_client_id_description'))
                    ->yup(Yup::string()->required()),
                Builder::divider(),
                Builder::text('video.mux.client_secret')
                    ->required()
                    ->label(__p('video::phrase.mux_client_secret'))
                    ->description(__p('video::phrase.mux_client_secret_description'))
                    ->yup(Yup::string()->required()),
                Builder::divider(),
                Builder::text('video.mux.webhook_secret')
                    ->required()
                    ->label(__p('video::phrase.mux_webhook_secret'))
                    ->description(__p('video::phrase.mux_webhook_secret_description', [
                        'muxLink'        => Mux::MUX_WEBHOOK_SETTING_PATH,
                        'muxCallbackUrl' => apiUrl('video.callback', ['provider' => 'mux'], true),
                    ]))
                    ->yup(Yup::string()->required()),
            );
        $this->addDefaultFooter(true);
    }
}
