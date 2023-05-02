<?php

namespace MetaFox\Mfa\Http\Resources\v1\UserService;

use MetaFox\Form\Mobile\Builder;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class DeactivateServiceMobileForm.
 */
class DeactivateServiceMobileForm extends DeactivateServiceForm
{
    protected function handlePasswordFields(): array
    {
        return [
            Builder::password('password')
                ->autoComplete('off')
                ->marginNormal()
                ->label(__p('user::phrase.current_password'))
                ->placeholder(__p('user::phrase.current_password'))
                ->required()
                ->yup(
                    Yup::string()->required(__p('user::phrase.field_password_is_a_required', [
                        'field' => __p('user::phrase.current_password'),
                    ]))
                ),
        ];
    }
}
