<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\Account;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Rules\MetaFoxPasswordFormatRule;
use MetaFox\User\Models\User;
use MetaFox\Yup\StringShape;
use MetaFox\Yup\Yup;

/**
 * Class EditPasswordForm.
 * @property ?User $resource
 */
class EditPasswordForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this
            ->asPut()
            ->action(url_utility()->makeApiUrl('/account/setting'))
            ->setValue([]);
    }

    protected function getPasswordValidate(string $field): StringShape
    {
        $passwordValidate = Yup::string()
            ->required(__p('user::phrase.field_password_is_a_required', [
                'field' => $field,
            ]));

        $passwordRule = new MetaFoxPasswordFormatRule();

        foreach ($passwordRule->getFormRules() as $rule) {
            $passwordValidate->matches($rule, $passwordRule->message());
        }

        return $passwordValidate;
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::password('old_password')
                ->autoComplete('off')
                ->marginNormal()
                ->label(__p('user::phrase.current'))
                ->placeholder(__p('user::phrase.current_password'))
                ->required()
                ->yup(Yup::string()->required(__p('user::phrase.field_password_is_a_required', [
                    'field' => __p('user::phrase.current'),
                ]))),
            Builder::password('new_password')
                ->label(__p('user::phrase.new'))
                ->variant('outlined')
                ->fullWidth(true)
                ->required()
                ->marginDense()
                ->sizeSmall()
                ->autoComplete('password')
                ->placeholder(__p('user::phrase.new_password'))
                ->minLength(Settings::get('user.minimum_length_for_password', 8))
                ->maxLength(Settings::get('user.maximum_length_for_password', 30))
                ->yup($this->getPasswordValidate(__p('user::phrase.new'))),
            Builder::password('new_password_confirmation')
                ->label(__p('core::phrase.confirm'))
                ->variant('outlined')
                ->fullWidth(true)
                ->required()
                ->marginDense()
                ->sizeSmall()
                ->autoComplete('password')
                ->placeholder(__p('user::phrase.confirm_password'))
                ->minLength(Settings::get('user.minimum_length_for_password', 8))
                ->maxLength(Settings::get('user.maximum_length_for_password', 30))
                ->yup($this->getPasswordValidate(__p('core::phrase.confirm'))),
        );

        $footer = $this->addFooter(['separator' => false]);

        $footer->addFields(
            Builder::submit()->label(__p('core::phrase.save'))->variant('contained'),
            Builder::cancelButton()->label(__p('core::phrase.cancel'))->variant('outlined'),
        );
    }
}
