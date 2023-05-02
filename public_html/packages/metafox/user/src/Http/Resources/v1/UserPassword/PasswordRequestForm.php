<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\UserPassword;

use MetaFox\Captcha\Support\Facades\Captcha;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Yup\Yup;

/**
 * @preload 0
 */
class PasswordRequestForm extends AbstractForm
{
    public function boot(): void
    {
        if (Settings::get('user.shorter_reset_password_routine')) {
            $this->submitAction('user/forgotPassword');
        }
    }

    protected function prepare(): void
    {
        $this->title(__p('user::phrase.forgot_password'))
            ->description(__p('user::phrase.enter_email_search_account'))
            ->action(apiUrl('user.password.request.method', ['resolution' => 'web']))
            ->asPost();
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::email('email')
                ->label(__p('core::phrase.email_address'))
                ->placeholder(__p('core::phrase.email_address'))
                ->description(__p('user::phrase.forgot_email_help'))
                ->required()
                ->yup(
                    Yup::string()
                        ->email(__p('validation.invalid_email_address'))
                        ->required()
                ),
            Captcha::getFormField('user.forgot_password')
        );

        $this->addFooter()
            ->addFields(
                Builder::submit()
                    ->sizeMedium()
                    ->label(__p('user::phrase.request_new_password')),
            );
    }
}
