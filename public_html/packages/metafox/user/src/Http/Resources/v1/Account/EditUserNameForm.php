<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\Account;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\User\Models\User;
use MetaFox\Yup\Yup;

/**
 * Class EditUserNameForm.
 * @property ?User $resource
 */
class EditUserNameForm extends AbstractForm
{
    protected function prepare(): void
    {
        $value = $this->resource ? [
            'user_name' => $this->resource->user_name,
        ] : null;
        $this
            ->asPut()
            ->action(url_utility()->makeApiUrl('/account/setting'))
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::text('user_name')
                ->marginNormal()
                ->label(__p('core::phrase.username'))
                ->placeholder(__p('user::phrase.choose_a_username'))
                ->autoComplete('off')
                ->required()
                ->yup(
                    Yup::string()
                        ->label(__p('core::phrase.user_name'))
                        ->required()
                        ->matches('^[a-zA-Z0-9_\-\x7f-\xff]+$', __p('validation.invalid_username_format'))
                        ->minLength(Settings::get('user.min_length_for_username', 5), '${path} must be at least ${min} characters')
                        ->maxLength(Settings::get('user.max_length_for_username'))
                ),
        );

        $footer = $this->addFooter(['separator' => false]);

        $footer->addFields(
            Builder::submit()->label(__p('core::phrase.save'))->variant('contained'),
            Builder::cancelButton()->label(__p('core::phrase.cancel'))->variant('outlined'),
        );
    }
}
