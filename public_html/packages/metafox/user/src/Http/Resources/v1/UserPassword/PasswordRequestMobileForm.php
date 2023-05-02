<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\UserPassword;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Yup\Yup;

/**
 * @driverType form-mobile
 * @driverName user.forgot_password
 * @preload    1
 */
class PasswordRequestMobileForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('user::phrase.forgot_password'))
            ->description(__p('user::phrase.enter_email_search_account'))
            ->action(apiUrl('user.password.request.method', ['resolution' => 'mobile']))
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
                    Yup::string()->format('email', __p('validation.invalid_email_address'))
                ),
        );

        $this->addFooter()
            ->addFields(
                Builder::submit()->label(__p('user::phrase.request_new_password'))->sizeMedium(),
            );
    }
}
