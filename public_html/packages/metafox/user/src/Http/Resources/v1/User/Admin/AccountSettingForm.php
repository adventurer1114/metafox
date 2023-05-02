<?php

namespace MetaFox\User\Http\Resources\v1\User\Admin;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use MetaFox\Authorization\Repositories\Contracts\RoleRepositoryInterface;
use MetaFox\Core\Support\Facades\Language;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\FormField;
use MetaFox\Form\GenderTrait;
use MetaFox\Form\Html\Dropdown;
use MetaFox\Form\Section;
use MetaFox\Notification\Repositories\Eloquent\NotificationChannelRepository;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Rules\MetaFoxPasswordFormatRule;
use MetaFox\Platform\UserRole;
use MetaFox\Profile\Repositories\ProfileRepositoryInterface;
use MetaFox\User\Models\User as Model;
use MetaFox\User\Models\UserProfile;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Repositories\UserPrivacyRepositoryInterface;
use MetaFox\User\Support\Browse\Scopes\User\CustomFieldScope;
use MetaFox\User\Support\Facades\User as UserFacade;
use MetaFox\Yup\StringShape;
use MetaFox\Yup\Yup;

/**
 * Class AccountSettingForm.
 * @property Model $resource
 * @driverType form
 * @driverName user.update
 */
class AccountSettingForm extends AbstractForm
{
    use GenderTrait;

    public const MODULE_KEY = 'module_id';

    public const VAR_NAME_KEY = 'var_name';

    public function boot(int $id, UserRepositoryInterface $repository): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $user    = $this->resource;
        $profile = $this->resource->profile;
        $gender  = $profile->gender;

        $values = array_merge(
            [
                'full_name'         => $user->full_name,
                'user_name'         => $user->user_name,
                'email'             => $user->email,
                'birthday'          => $profile->birthday,
                'postal_code'       => $profile->postal_code,
                'country_city_code' => $this->getCityCode($profile),
                'gender'            => $gender?->is_custom ? 0 : $profile->gender?->entityId(),
                'custom_gender'     => $gender?->is_custom ? $profile->gender?->entityId() : 0,
                'country_iso'       => $profile->country_iso,
                'country_state_id'  => $profile->country_state_id,
                'language_id'       => $profile->language_id,
                'address'           => $profile->address,
            ],
            resolve(ProfileRepositoryInterface::class)->denormalize($this->resource),
            $this->getDefaultValue()
        );

        if ($this->hasRoleField()) {
            Arr::set($values, 'role_id', $user->roleId());
        }

        $this->action('admincp/user/' . $this->resource->id)
            ->asPatch()
            ->setValue($values);
    }

    public function initialize(): void
    {
        $context         = user();
        $minYear         = Settings::get('user.date_of_birth_start', 1900);
        $maxYear         = Settings::get('user.date_of_birth_end', Carbon::now()->year);
        $minDate         = Carbon::create($minYear);
        $maxDate         = Carbon::create($maxYear);
        $minDateString   = $minDate ? $minDate->toDateString() : $minYear;
        $maxDateString   = $maxDate ? $maxDate->endOfYear()->toDateString() : $maxYear;
        $birthdayMessage = __p('validation.invalid_date_of_birth_between', [
            'date_start' => $minDateString,
            'date_end'   => $maxDateString,
        ]);

        $this->title(__p('core::phrase.edit'));

        $basic = $this->addBasic();

        $basic->addFields(
            Builder::typography('basic_information_typo')
                ->variant('h5')
                ->plainText(__p('user::phrase.basic_information')),
            Builder::text('full_name')
                ->required()
                ->label(__p('core::phrase.full_name'))
                ->yup(
                    Yup::string()
                        ->required()
                ),
            Builder::text('user_name')
                ->required()
                ->label(__p('core::phrase.username'))
                ->yup(
                    Yup::string()
                        ->required()
                ),
            Builder::password('password')
                ->label(__p('core::phrase.password'))
                ->minLength(Settings::get('user.minimum_length_for_password', 8))
                ->maxLength(Settings::get('user.maximum_length_for_password', 30))
                ->yup($this->getPasswordValidate()),
            Builder::text('email')
                ->required()
                ->label(__p('core::phrase.email_address'))
                ->yup(
                    Yup::string()
                        ->email(__p('validation.invalid_email_address'))
                        ->required()
                ),
            $this->buildRoleField(),
            Builder::countryState('country_iso')
                ->valueType('array')
                ->setAttribute('countryFieldName', 'country_iso')
                ->setAttribute('stateFieldName', 'country_state_id'),
            //City field
            Builder::countryCity('country_city_code')
                ->label(__p('localize::country.city'))
                ->description(__p('localize::country.city_name'))
                ->searchEndpoint('user/city')
                ->searchParams([
                    'country' => ':country_iso',
                    'state'   => ':country_state_id',
                ]),
            //Address field
            Builder::text('address')
                ->label(__p('user::phrase.address')),
            Builder::text('postal_code')
                ->label(__p('user::phrase.zip_postal_code'))
                ->placeholder('- - - - - -'),
            Builder::gender('gender')
                ->label(__p('user::phrase.user_gender'))
                ->options($this->getDefaultGenders($context)),
            Builder::customGenders('custom_gender')
                ->label(__p('user::phrase.custom_gender'))
                ->showWhen(['and', ['eq', 'gender', 0]])
                ->options($this->getCustomGenders($context))
                ->yup(
                    Yup::when('gender')
                        ->is(0)
                        ->then(
                            Yup::number()
                                ->required(__p('user::validation.custom_gender_is_a_required_field'))
                        )
                ),
            Builder::birthday('birthday')
                ->label(__p('user::phrase.birthday'))
                ->setAttribute('minDate', $minDateString)
                ->setAttribute('maxDate', $maxDateString)
                ->yup(
                    Yup::date()
                        ->nullable(true)
                        ->minYear((string) $minYear, $birthdayMessage)
                        ->maxYear((string) $maxYear, $birthdayMessage)
                        ->setError('typeError', __p('core::phrase.invalid_date'))
                ),
            Builder::choice('language_id')
                ->marginNormal()
                ->label(__p('core::phrase.primary_language'))
                ->placeholder(__p('core::phrase.primary_language'))
                ->autoComplete('off')
                ->required()
                ->options(Language::getActiveOptions())
                ->yup(Yup::string()->required()),
        );

        $this->buildProfilePictureField($basic);

        $this->buildPrivacyField($basic);

        $this->buildNotificationSections();

        $this->buildCustomField($basic);

        $this->addDefaultFooter(true);
    }

    private function getDefaultValue(): array
    {
        $default = [];

        $default['privacy']      = $this->getDefaultPrivacyValue();
        $default['notification'] = $this->getDefaultNotificationValue();
        $default['avatar']       = ['url' => $this->resource->profile->avatar];

        return $default;
    }

    private function buildProfilePictureField(Section $basic)
    {
        $basic->addField(
            Builder::typography('profile_picture_typo')
                ->variant('h5')
                ->plainText(__p('user::phrase.profile_picture'))
        );

        $basic->addField(
            Builder::avatarUpload('avatar')
                ->label(__p('user::phrase.profile_image'))
                ->placeholder(__p('user::phrase.profile_image'))
                ->description(__p('user::phrase.profile_image_desc'))
                ->yup(
                    Yup::object()->addProperty(
                        'base64',
                        Yup::string()
                    )->nullable()
                )
        );
    }

    private function getProfilePrivacy(): array
    {
        $user      = $this->resource;
        $privacies = resolve(UserPrivacyRepositoryInterface::class)
            ->getProfileSettings($user->entityId());

        $privacies[] = resolve(UserPrivacyRepositoryInterface::class)
            ->getBirthdaySetting($user);

        return $privacies;
    }

    private function getNotificationChannel(): array
    {
        return resolve(NotificationChannelRepository::class)
            ->getActiveChannelNames();
    }

    private function getDefaultNotificationValue(): array
    {
        $values = [];

        foreach ($this->getNotificationChannel() as $channel) {
            $values[$channel] = $this->getDefaultNotificationValueByChannel($channel);
        }

        return $values;
    }

    private function getDefaultNotificationValueByChannel(string $channel): array
    {
        $moduleKey  = static::MODULE_KEY;
        $varNameKey = static::VAR_NAME_KEY;

        $settings = UserFacade::getNotificationSettingsByChannel($this->resource, $channel);

        $valueModule  = [];
        $valueVarName = [];

        foreach ($settings as $setting) {
            $this->getValueNotification($setting, $valueModule, $moduleKey);

            foreach (Arr::get($setting, 'type') as $type) {
                $this->getValueNotification($type, $valueVarName, $varNameKey);
            }
        }

        return [
            $moduleKey  => $valueModule,
            $varNameKey => $valueVarName,
        ];
    }

    private function getValueNotification(array $params, array &$values, string $key): void
    {
        $key          = Arr::get($params, $key);
        $values[$key] = (int) Arr::get($params, 'value');
    }

    private function getDefaultPrivacyValue(): array
    {
        $values = [];
        foreach ($this->getProfilePrivacy() as $privacy) {
            $values[$privacy[static::VAR_NAME_KEY]] = $privacy['value'];
        }

        return $values;
    }

    private function buildNotificationSwitchField(Section $basic, string $channel): void
    {
        $settings = UserFacade::getNotificationSettingsByChannel($this->resource, $channel);

        foreach ($settings as $setting) {
            $this->buildModuleField($basic, $setting);
            $this->buildVarNameField($basic, $setting);
        }
    }

    private function buildNotificationSection(string $channel): void
    {
        $container = $this->addSection("notification_{$channel}")
            ->label(__p("notification::phrase.{$channel}_notifications"))
            ->collapsible();

        $this->buildNotificationSwitchField($container, $channel);
    }

    private function buildNotificationSections(): void
    {
        foreach ($this->getNotificationChannel() as $channel) {
            $this->buildNotificationSection($channel);
        }
    }

    private function getFieldNameNotification(array $setting, string $keyName, string $name = null): string
    {
        $channel = Arr::get($setting, 'channel');

        if (null == $name) {
            $name = Arr::get($setting, $keyName);
        }

        return sprintf('notification.%s.%s.%s', $channel, $keyName, $name);
    }

    private function buildModuleField(Section $basic, array $setting): void
    {
        $moduleKey  = static::MODULE_KEY;
        $moduleName = $this->getFieldNameNotification($setting, $moduleKey);

        $basic->addField(
            Builder::switch($moduleName)
                ->label(Arr::get($setting, 'phrase'))
        );
    }

    private function buildVarNameField(Section $basic, array $setting): void
    {
        $moduleKey  = static::MODULE_KEY;
        $varNameKey = static::VAR_NAME_KEY;

        foreach (Arr::get($setting, 'type') as $type) {
            $moduleName = $this->getFieldNameNotification($setting, $moduleKey);
            $varName    = $this->getFieldNameNotification($setting, $varNameKey, Arr::get($type, $varNameKey));

            $basic->addField(
                Builder::switch($varName)
                    ->label(Arr::get($type, 'phrase'))
                    ->showWhen(['truthy', $moduleName])
            );
        }
    }

    private function buildPrivacyField(Section $basic): void
    {
        $basic->addField(
            Builder::typography('profile_privacy_typo')
                ->variant('h5')
                ->plainText(__p('core::web.profile_privacy'))
        );

        foreach ($this->getProfilePrivacy() as $setting) {
            $basic->addField(
                new Dropdown([
                    'name'    => 'privacy.' . $setting['var_name'],
                    'label'   => $setting['phrase'],
                    'options' => $setting['options'],
                ])
            );
        }
    }

    private function buildCustomField(Section $basic): void
    {
        $basic->addField(
            Builder::typography('custom_fields_typo')
                ->variant('h5')
                ->plainText(__p('user::phrase.custom_fields'))
        );

        $fields = CustomFieldScope::getAllowCustomFields();

        foreach ($fields as $field) {
            $formField = $field->toEditField();

            $formField->description(null);

            $basic->addFields($formField);
        }
    }

    protected function buildRoleField(): ?FormField
    {
        if (!$this->hasRoleField()) {
            return null;
        }

        $roleOptions = array_filter(resolve(RoleRepositoryInterface::class)->getRoleOptions(), function ($role) {
            return Arr::get($role, 'value') != UserRole::SUPER_ADMIN_USER;
        });

        return Builder::choice('role_id')
            ->multiple(false)
            ->disableClearable()
            ->label(__p('core::phrase.role'))
            ->options(array_values($roleOptions))
            ->yup(
                Yup::number()
                    ->positive()
                    ->required()
            );
    }

    protected function hasRoleField(): bool
    {
        return $this->resource && !$this->resource->hasSuperAdminRole();
    }

    public function getCityCode(?UserProfile $profile): ?array
    {
        if (!$profile->country_city_code) {
            return null;
        }

        return [
            'label' => $profile->city_location,
            'value' => $profile->country_city_code,
        ];
    }

    protected function getPasswordValidate(): StringShape
    {
        $passwordRule = new MetaFoxPasswordFormatRule();

        return Yup::string()
            ->setError('typeError', __p('validation.password_is_a_required_field'))
            ->setError('minLength', '${path} must be at least ${min} characters')
            ->matchesArray($passwordRule->getFormRules(), $passwordRule->message());
    }
}
