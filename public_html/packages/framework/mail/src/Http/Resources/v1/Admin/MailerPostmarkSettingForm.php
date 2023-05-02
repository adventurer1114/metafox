<?php

namespace MetaFox\Mail\Http\Resources\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use MetaFox\Form\AbstractForm as Form;
use MetaFox\Form\Builder;
use MetaFox\Mail\Mails\VerifyConfig;
use MetaFox\Platform\Facades\Settings;

/**
 * @driverType mailer
 * @driverName postmark
 */
class MailerPostmarkSettingForm extends Form
{
    protected function prepare(): void
    {
        $module = 'core';

        $vars = [
            'core.services.postmark.token',
            'mail.test_email',
            'mail.from',
        ];

        $value = [];

        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get($var));
        }

        $this->title(__p('core::phrase.mailer_postmark_settings'))
            ->action('admincp/setting/' . $module)
            ->asPost()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::text('mail.mailers.postmark.token')
                ->required()
                ->label(__p('mail::phrase.postmark_token_label'))
                ->description(__p('mail::phrase.postmark_token_desc')),
            Builder::text('mail.from.name')
                ->required()
                ->autoComplete('off')
                ->label(__p('mail::phrase.mail_from_label'))
                ->description(__p('mail::phrase.mail_from_desc'))
                ->placeholder('admin'),
            Builder::text('mail.from.address')
                ->required()
                ->autoComplete('off')
                ->label(__p('mail::phrase.mail_from_address_label'))
                ->description(__p('mail::phrase.mail_from_address_desc'))
                ->placeholder('name@your-domain.com'),
            Builder::text('mail.test_email')
                ->required()
                ->autoComplete('off')
                ->label('Test Email'),
        );

        $this->addDefaultFooter(true);
    }

    /**
     * @param Request $request
     *
     * @return array<mixed>
     */
    public function validated(Request $request): array
    {
        $data = $request->validate([
            'core.services.postmark.token' => 'required|string',
            'mail.from.address'            => 'string|required',
            'mail.from.name'               => 'string|required',
            'mail.test_email'              => 'string|required',
        ]);

        Arr::set($data, 'mail.mailers.postmark.transport', 'portmark');

        config([
            'services.postmark'          => Arr::get($data, 'core.serivices.postmark'),
            'mail.mailers.verify_config' => Arr::get($data, 'mail.mailers.postmark'),
        ]);

        Mail::mailer('postmark')
            ->send(new VerifyConfig(Arr::get($data, 'mail')));

        return $data;
    }
}
