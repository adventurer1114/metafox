<?php

namespace MetaFox\User\Http\Resources\v1\User;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Core\Support\Facades\Currency;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Form\Mobile\MobileForm as AbstractForm;
use MetaFox\Platform\Facades\Settings;
use MetaFox\User\Models\User as Model;
use MetaFox\User\Policies\UserPolicy;
use MetaFox\Yup\Yup;

/**
 * @property Model $resource
 * @driverName user.account.info
 * @driverType form-mobile
 */
class AccountSettingMobileForm extends AbstractForm
{
    /**
     * @throws AuthenticationException
     */
    public function boot(): void
    {
        /** @var Model $context */
        $context        = user();
        $this->resource = $context;

        policy_authorize(UserPolicy::class, 'update', $context, $this->resource);
    }

    protected function prepare(): void
    {
        if (!$this->resource instanceof Model) {
            return;
        }

        $profile = $this->resource->profile;

        $this
            ->title(__p('user::phrase.edit_account'))
            ->action('user/' . $this->resource->entityId())
            ->asPut()
            ->setValue([
                'profile' => [
                    'language_id'  => $profile->language_id,
                    'currency_id'  => $profile->currency_id,
                    'phone_number' => $profile->phone_number,
                ],
                'full_name' => $this->resource->full_name,
                'user_name' => $this->resource->user_name,
                'email'     => $this->resource->email,
            ]);
    }

    public function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('full_name')
                    ->required()
                    ->variant('standardInlined')
                    ->label(__p('core::phrase.full_name'))
                    ->placeholder(__p('user::phrase.full_name'))
                    ->yup(
                        Yup::string()->required()
                            ->maxLength(Settings::get('user.maximum_length_for_full_name')),
                    ),
                Builder::text('user_name')
                    ->variant('standardInlined')
                    ->required()
                    ->label(__p('core::phrase.username'))
                    ->placeholder(__p('user::phrase.choose_a_username'))
                    ->yup(
                        Yup::string()
                            ->label(__p('core::phrase.user_name'))
                            ->required()
                            ->matches('^[a-zA-Z0-9_\-\x7f-\xff]+$', __p('validation.invalid_username_format'))
                            ->minLength(
                                Settings::get('user.min_length_for_username', 5),
                                '${path} must be at least ${min} characters'
                            )
                            ->maxLength(Settings::get('user.max_length_for_username'))
                    ),
                Builder::text('email')
                    ->variant('standardInlined')
                    ->required()
                    ->label(__p('core::phrase.email_address'))
                    ->placeholder(__p('core::phrase.email_address'))
                    ->yup(
                        Yup::string()
                            ->email(__p('validation.invalid_email_address'))
                            ->required()
                    ),
                Builder::phoneNumber('profile.phone_number'),
                Builder::choice('profile.currency_id')
                    ->required()
                    ->label(__p('core::phrase.preferred_currency'))
                    ->options(Currency::getActiveOptions())
            );
        $this->addCancelAccountSection();
    }

    protected function addCancelAccountSection(): void
    {
        if ($this->resource->hasSuperAdminRole()) {
            return;
        }

        $this->addSection(['name' => 'manage_account'])
            ->label(__p('user::phrase.manage_account'))
            ->addFields(
                Builder::clickable('cancel_account')
                    ->action('getCancelAccountForm')
                    ->label(__p('user::phrase.cancel_account'))
                    ->severity('danger')
                    ->params([
                        'id' => $this->resource->entityId(),
                    ]),
            );
    }
}
