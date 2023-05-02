<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\User;

use MetaFox\Captcha\Support\Facades\Captcha;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Section;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Yup\Yup;

/**
 * @driverName user.small_login
 * @preload    1
 */
class SmallUserLoginForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this
            ->title('')
            ->testId('login form')
            ->noBreadcrumb(true)
            ->submitAction('@login')
            ->alertPreSubmitErrors(__p('user::validation.invalid_email_and_password'))
            ->action(url_utility()->makeApiUrl('user/login'))
            ->asPost();
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic()->asHorizontal()->justifyContent('end');

        $basic->addFields(
            Builder::text('email')
                ->component(MetaFoxForm::TEXT)
                ->variant('outlined')
                ->label(__p('user::phrase.username_or_email'))
                ->fullWidth(false)
                ->placeholder(__p('user::phrase.enter_your_username_or_email'))
                ->marginDense()
                ->autoComplete('email')
                ->sizeSmall()
                ->noFeedback(false)
                ->showErrorTooltip(true)
                ->autoFocus(true),
            Builder::password('password')
                ->label(__p('user::phrase.password'))
                ->variant('outlined')
                ->fullWidth(false)
                ->marginDense()
                ->sizeSmall()
                ->autoComplete('password')
                ->placeholder(__p('user::phrase.password'))
                ->noFeedback(false)
                ->showErrorTooltip(true),
            Builder::checkbox('remember')
                ->checkedValue(true)
                ->fullWidth(false)
                ->label(__p('user::web.remember_me')),
            Captcha::getFormField('user.user_login', 'web', true),
        );

        $basic->addFields(
            Builder::submit('login')
                ->marginDense()
                ->type('submit')
                ->sizeMedium()
                ->label(__p('user::phrase.sign_in'))
                ->color('primary')
                ->variant('contained')
                ->fullWidth(false),
        );

        if (Settings::get('user.allow_user_registration')) {
            $basic->addFields(
                Builder::linkButton('register')
                    ->link('/register')
                    ->variant('link')
                    ->sizeMedium()
                    ->marginDense()
                    ->color('primary')
                    ->fullWidth(false)
                    ->label(__p('user::phrase.don_t_have_an_account')),
            );
        }
    }
}
