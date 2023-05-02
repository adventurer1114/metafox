<?php

namespace MetaFox\Twilio\Http\Resources\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;

/**
 * Class ServiceTwilioSettingForm.
 * @codeCoverageIgnore
 * @ignore
 */
class ServiceTwilioSettingForm extends AbstractForm
{
    /**
     * @var string
     */
    private $namespace = 'sms.services.twilio';

    protected function prepare(): void
    {
        $value = Arr::only(Settings::get($this->namespace), [
            'sid',
            'auth_token',
            'number',
        ]);

        $this->title(__p('twilio::admin.twilio_settings'))
            ->action('admincp/sms/service/twilio')
            ->asPut()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::text('sid')
                ->required()
                ->autoComplete('off')
                ->label(__p('twilio::admin.account_sid')),
            Builder::text('auth_token')
                ->required()
                ->autoComplete('off')
                ->label(__p('twilio::admin.auth_token')),
            Builder::text('number')
                ->required()
                ->autoComplete('off')
                ->label(__p('twilio::admin.number')),
        );

        $this->addDefaultFooter(true);
    }

    /**
     * validated.
     *
     * @param  Request      $request
     * @return array<mixed>
     */
    public function validated(Request $request): array
    {
        $data = $request->validate([
            'sid'        => ['required', 'string'],
            'auth_token' => ['required', 'string'],
            'number'     => ['required', 'string'],
        ]);

        // config([
        //     'sms.services.verify_config' => Arr::get($data, 'sms.services.log'),
        // ]);

        // Sms::service('verify_config')
        //     ->send(new VerifyConfig(Arr::get($data, 'sms')));

        return [
            $this->namespace => array_merge($data, [
                'service' => 'twilio',
                'is_core' => false,
            ]),
        ];
    }
}
