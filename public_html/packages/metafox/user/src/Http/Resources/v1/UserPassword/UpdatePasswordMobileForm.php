<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\UserPassword;

use Illuminate\Support\Arr;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\MultiStepFormTrait;
use MetaFox\Platform\Facades\Settings;
use MetaFox\User\Models\User;
use MetaFox\Yup\StringShape;
use MetaFox\Yup\Yup;
use MetaFox\Platform\Rules\MetaFoxPasswordFormatRule;
use MetaFox\User\Http\Requests\v1\UserPassword\UpdateRequest;

/**
 * @property User $resource
 * @driverType form
 * @driverName user.password.edit
 * @resolution mobile
 * @preload    0
 */
class UpdatePasswordMobileForm extends AbstractForm
{
    use MultiStepFormTrait;

    protected string $token;

    public function boot(UpdateRequest $request): void
    {
        $params      = $request->validated();
        $this->token = Arr::get($params, 'token');
        $this->type('formSchema');
    }

    protected function prepare(): void
    {
        $this->title(__p('user::phrase.choose_a_new_password'))
            ->action(apiUrl('user.password.reset', ['resolution' => 'mobile']))
            ->setValue([
                'user_id' => $this->resource->entityId(),
                'token'   => $this->token,
            ])
            ->secondAction('@auth/logout')
            ->asPatch();
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::password('new_password')
                ->label(__p('user::phrase.new'))
                ->variant('outlined')
                ->fullWidth(true)
                ->required()
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
                ->sizeSmall()
                ->autoComplete('password')
                ->placeholder(__p('user::phrase.confirm_password'))
                ->minLength(Settings::get('user.minimum_length_for_password', 8))
                ->maxLength(Settings::get('user.maximum_length_for_password', 30))
                ->yup($this->getPasswordValidate(__p('core::phrase.confirm'))),
            Builder::hidden('user_id'),
            Builder::hidden('token'),
        );
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
}
