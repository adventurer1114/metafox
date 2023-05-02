<?php

namespace MetaFox\Mfa\Http\Resources\v1\UserService;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Mfa\Models\UserAuthToken as Model;
use MetaFox\Mfa\Repositories\UserServiceRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class AuthenticatorAuthForm.
 * @property ?Model $resource
 */
class AuthenticatorAuthForm extends AbstractForm
{
    private function userServiceRepository(): UserServiceRepositoryInterface
    {
        return resolve(UserServiceRepositoryInterface::class);
    }
    protected function prepare(): void
    {
        $user = $this->resource?->user;
        if (empty($this->resource) || !$user instanceof User) {
            return;
        }

        // TODO: support multiple MFA services in a single form
        $userService = $this->userServiceRepository()->getService($user, 'authenticator');

        $this->title(__p('mfa::phrase.authenticator'))
            ->description(__p('mfa::phrase.authenticator_login_description'))
            ->action(apiUrl('mfa.user.auth.auth'))
            ->secondAction('@loginAuthentication')
            ->asPost()
            ->setValue([
                'service'  => $userService?->service,
                'password' => $this->resource->value,
            ]);
    }

    protected function initialize(): void
    {
        if (empty($this->resource)) {
            return;
        }

        $basic = $this->addBasic();

        $basic->addFields(
            Builder::numberCode('verification_code')
                ->required()
                ->yup(
                    Yup::string()
                        ->required()
                        ->minLength(6, __p('mfa::phrase.authenticator_code_must_be_a_number_with_six_digits'))
                        ->matchesAsNumeric(__p('mfa::phrase.authenticator_code_must_be_a_number_with_six_digits'), false)
                        ->setError('required', __p('mfa::phrase.authenticator_code_is_a_required_field'))
                ),
        );

        $this->addFooter()
            ->addFields(
                Builder::submit()
                    ->label(__p('mfa::phrase.verify'))
                    ->disableWhenClean()
            );
    }
}
