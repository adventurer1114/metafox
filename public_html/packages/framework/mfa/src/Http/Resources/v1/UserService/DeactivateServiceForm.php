<?php

namespace MetaFox\Mfa\Http\Resources\v1\UserService;

use Illuminate\Http\Request;
use MetaFox\Form\AbstractField;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class DeactivateServiceForm.
 */
class DeactivateServiceForm extends AbstractForm
{
    private string $service;

    public function boot(Request $request): void
    {
        $this->service = $request->get('service', '');
    }

    protected function prepare(): void
    {
        $this->action(apiUrl('mfa.user.service.deactivate'))
            ->asDelete()
            ->setValue([
                'service' => $this->service,
            ]);
    }

    protected function initialize(): void
    {
        $this->title(__p('mfa::phrase.turn_off_two_factor_authentication_label'));

        $basic = $this->addBasic();

        $basic->addFields(
            Builder::typography('description')
                ->plainText(__p('mfa::phrase.turn_off_two_factor_authentication_desc')),
        );

        if (Settings::get('mfa.confirm_password')) {
            $basic->addFields(
                ...$this->handlePasswordFields(),
            );
        }

        $this->addFooter()
            ->addFields(
                Builder::submit()
                    ->label(__p('core::phrase.delete'))
            );
    }

    /**
     * handlePasswordFields.
     *
     * @return array<AbstractField>
     */
    protected function handlePasswordFields(): array
    {
        return [
            Builder::typography('type_current_password')
                ->plainText(__p('user::phrase.current_password')),
            Builder::password('password')
                ->autoComplete('off')
                ->marginNormal()
                ->label(__p('user::phrase.current'))
                ->placeholder(__p('user::phrase.current_password'))
                ->required()
                ->yup(
                    Yup::string()->required(__p('user::phrase.field_password_is_a_required', [
                        'field' => __p('user::phrase.current'),
                    ]))
                ),
        ];
    }
}
