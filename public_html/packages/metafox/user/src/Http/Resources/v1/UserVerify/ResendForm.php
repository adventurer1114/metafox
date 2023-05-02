<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\UserVerify;

use MetaFox\Captcha\Support\Facades\Captcha;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Html\Hidden;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Yup\Yup;

/**
 * Class ResendForm.
 * @driverName verify.resend
 * @preload    1
 */
class ResendForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this
            ->title('')
            ->noBreadcrumb(true)
            ->asPost()
            ->submitAction('@user/verify/resendEmail')
            ->action(url_utility()->makeApiUrl('user/verify/resend'))
            ->setValue([]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::email('email')
                ->component(MetaFoxForm::TEXT)
                ->variant('outlined')
                ->label(__p('core::phrase.email'))
                ->required()
                ->fullWidth(true)
                ->placeholder(__p('user::phrase.enter_your_email'))
                ->marginNormal()
                ->autoComplete('email')
                ->autoFocus(true)
                ->yup(
                    Yup::string()
                        ->email(__p('validation.invalid_email_address'))
                        ->required()
                ),
        );

        $this->addFooter()
            ->setAttribute('separator', false)
            ->addFields(
                Builder::submit()
                    ->label(__p('core::phrase.submit'))
            );
    }
}
