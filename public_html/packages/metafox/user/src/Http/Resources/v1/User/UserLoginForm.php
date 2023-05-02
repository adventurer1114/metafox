<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\User;

use MetaFox\Captcha\Support\Facades\Captcha;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Html\Hidden;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Form\Section;
use MetaFox\Yup\Yup;

/**
 * Class UserLoginForm.
 * @driverName user.login
 * @preload    1
 */
class UserLoginForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this
            ->title('')
            ->noBreadcrumb(true)
            ->submitAction('@login')
            ->action(url_utility()->makeApiUrl('user/login'))
            ->asPost()
            ->acceptPageParams(['returnUrl'])
            ->setValue([]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic()
            ->variant('horizontal');

        $basic->addFields(
            Builder::text('email')
                ->component(MetaFoxForm::TEXT)
                ->variant('outlined')
                ->label(__p('user::phrase.username_or_email'))
                ->required()
                ->fullWidth(true)
                ->placeholder(__p('user::phrase.enter_your_username_or_email'))
                ->marginNormal()
                ->autoComplete('email')
                ->autoFocus(true)
                ->yup(
                    Yup::string()
                        ->required()
                ),
            Builder::password('password')
                ->label(__p('user::phrase.password'))
                ->variant('outlined')
                ->required()
                ->fullWidth(true)
                ->marginNormal()
                ->autoComplete('password')
                ->placeholder(__p('user::phrase.enter_your_password'))
                ->yup(
                    Yup::string()
                        ->required()
                ),
            Builder::checkbox('remember')
                ->checkedValue(true)
                ->label(__p('user::web.remember_me')),
            Captcha::getFormField('user.user_login', 'web', true),
            Builder::submit('login')
                ->marginNormal()
                ->type('submit')
                ->sizeLarge()
                ->label(__p('user::phrase.sign_in'))
                ->color('primary')
                ->variant('contained')
                ->fullWidth(true),
            Builder::linkButton('forgotPassword')
                ->link('/user/password/request')
                ->variant('link')
                ->sizeMedium()
                ->color('primary')
                ->marginNone()
                ->fullWidth(true)
                ->label(__p('user::phrase.forgot_password?')),
        );

        if (Settings::get('user.allow_user_registration')) {
            $basic->addField(
                Builder::linkButton('register')
                    ->link('/register')
                    ->sizeLarge()
                    ->variant('outlined')
                    ->marginDense()
                    ->color('primary')
                    ->fullWidth(true)
                    ->label(__p('user::phrase.don_t_have_an_account'))
                    ->sx(['pt' => 3]),
            );
        }

        $basic->addField(
            new Hidden(['name' => 'returnUrl'])
        );

        $this->handleSocialLoginFields($basic);
    }

    /**
     * @param Section $section
     *
     * @return void
     */
    protected function handleSocialLoginFields(Section $section)
    {
        $fieldResponses = array_filter(app('events')->dispatch('socialite.login_fields', ['web']) ?? []);
        if (empty($fieldResponses)) {
            return;
        }

        $section->addField(Builder::typography('social_login')
            ->setAttribute('class', 'typoSigInSocialite')
            ->plainText(__p('user::phrase.or_sign_in_using')));

        foreach ($fieldResponses as $response) {
            $section->addFields(...$response);
        }
    }
}
