<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\User;

use MetaFox\Captcha\Support\Facades\Captcha;
use MetaFox\Form\Mobile\MobileForm as AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Section;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Yup\Yup;

/**
 * Class UserLoginMobileForm.
 * @driverName user.login
 * @driverType form-mobile
 * @preload    1
 */
class UserLoginMobileForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title('')
            ->noBreadcrumb(true)
            ->submitAction('@login')
            ->action('user/login')
            ->asPost()
            ->acceptPageParams(['returnUrl'])
            ->setValue([]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::text('email')
                ->label(__p('user::phrase.username_or_email'))
                ->marginDense()
                ->required()
                ->placeholder(__p('user::phrase.enter_your_username_or_email'))
                ->autoFocus(true)
                ->fullWidth()
                ->yup(
                    Yup::string()
                        ->required(__p('validation.this_field_is_a_required_field'))
                ),
            Builder::password('password')
                ->label(__p('user::phrase.password'))
                ->marginDense()
                ->required()
                ->fullWidth()
                ->placeholder(__p('user::phrase.enter_your_password'))
                ->yup(
                    Yup::string()
                        ->required(__p('validation.password_is_a_required_field'))
                ),
            Captcha::getFormField('user.user_login', 'mobile', true),
        );

        $subActions = Builder::row('sub_actions');

        $subActions->setForm($this);

        $subActions->addFields(
            Builder::linkButton('changeAddress')
                ->margin('none')
                ->actionName('navigate')
                ->link('/site_address')
                ->fullWidth()
                ->label(__p('core::phrase.change_address')),
            Builder::linkButton('forgotPassword')
                ->link('/forgot_password')
                ->actionName('navigate')
                ->margin('none')
                ->fullWidth()
                ->label(__p('user::phrase.forgot_password?')),
        );

        $basic->addFields(
            Builder::submit('login')
                ->marginNormal()
                ->sizeNormal()
                ->label(__p('user::phrase.sign_in'))
                ->color('primary')
                ->fullWidth(),
            $subActions,
            Builder::hidden('returnUrl'),
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
        $fieldResponses = array_filter(app('events')->dispatch('socialite.login_fields', ['mobile']) ?? []);
        if (empty($fieldResponses)) {
            return;
        }

        foreach ($fieldResponses as $response) {
            $section->addFields(...$response);
        }
    }
}
