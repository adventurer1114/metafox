<?php

namespace MetaFox\User\Http\Resources\v1\User;

use MetaFox\Captcha\Support\Facades\Captcha;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Form\Section;
use MetaFox\User\Models\User as Model;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class LoginPopupUserForm.
 * @property ?Model $resource
 * @preload 1
 */
class LoginPopupUserForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this
            ->noBreadcrumb(true)
            ->submitAction('@login')
            ->action(url_utility()->makeApiUrl('user/login'))
            ->asPost();
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
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
                        ->required(__p('validation.this_field_is_a_required_field'))
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
        );

        $basic->addFields(
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
                ->margin('none')
                ->fullWidth(true)
                ->label(__p('user::phrase.forgot_password?')),
        );
    }
}
