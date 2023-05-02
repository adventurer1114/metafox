<?php

namespace MetaFox\User\Http\Resources\v1\User;

use Illuminate\Support\Carbon;
use MetaFox\Captcha\Support\Facades\Captcha;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\GenderTrait;
use MetaFox\Form\Section;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Rules\MetaFoxPasswordFormatRule;
use MetaFox\Yup\StringShape;
use MetaFox\Yup\Yup;

/**
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @preload 1
 */
class UserRegisterForm extends AbstractForm
{
    use GenderTrait;

    protected function prepare(): void
    {
        $this->title(__p('user::phrase.create_a_new_account'))
            ->action('/register')
            ->asPost()
            ->submitAction('user/register');
    }

    protected function getPasswordValidate(): StringShape
    {
        $passwordRule = new MetaFoxPasswordFormatRule();

        return Yup::string()
            ->required()
            ->setError('required', __p('validation.password_is_a_required_field'))
            ->setError('typeError', __p('validation.password_is_a_required_field'))
            ->setError('minLength', '${path} must be at least ${min} characters')
            ->matchesArray($passwordRule->getFormRules(), $passwordRule->message());
    }

    public function initialize(): void
    {
        $basic                = $this->addBasic();
        $isBasicFieldRequired = Settings::get('user.require_basic_field', false);
        $enableDateOfBirth    = Settings::get('user.enable_date_of_birth', false);
        $enableGender         = Settings::get('user.enable_gender', false);
        $enableLocation       = Settings::get('user.enable_location', false);
        $enableCity           = Settings::get('user.enable_city', false);
        $minYear              = Settings::get('user.date_of_birth_start', 1900);
        $maxYear              = Settings::get('user.date_of_birth_end', Carbon::now()->year);
        $minDate              = Carbon::create($minYear);
        $maxDate              = Carbon::create($maxYear);
        $minDateString        = $minDate ? $minDate->toDateString() : $minYear;
        $maxDateString        = $maxDate ? $maxDate->endOfYear()->toDateString() : $maxYear;
        $birthdayMessage      = __p('validation.invalid_date_of_birth_between', [
            'date_start' => $minDateString,
            'date_end'   => $maxDateString,
        ]);

        $context = user();

        if (Settings::get('user.force_user_to_upload_on_sign_up', false)) {
            $basic->addField(
                Builder::avatarUpload('user_profile')
                    ->required()
                    ->label(__p('user::phrase.profile_image'))
                    ->placeholder(__p('user::phrase.profile_image'))
                    ->description(__p('user::phrase.profile_image_desc'))
                    ->yup(
                        Yup::object()->addProperty(
                            'base64',
                            Yup::string()->required(__p('validation.field_is_a_required_field', [
                                'field' => __p('user::phrase.profile_image'),
                            ]))
                        )
                    )
            );
        }

        $basic->addFields(
            Builder::text('first_name')
                ->marginNormal()
                ->label(__p('user::phrase.first_name'))
                ->placeholder(__p('user::phrase.first_name'))
                ->returnKeyType('next')
                ->required()
                ->yup(
                    Yup::string()
                        ->required()
                        ->setError('required', __p('validation.first_name_is_a_required_field'))
                        ->setError('typeError', __p('validation.first_name_is_a_required_field')),
                ),
            Builder::text('last_name')
                ->marginNormal()
                ->label(__p('user::phrase.last_name'))
                ->placeholder(__p('user::phrase.last_name'))
                ->returnKeyType('next')
                ->required()
                ->yup(
                    Yup::string()
                        ->required()
                        ->setError('required', __p('validation.last_name_is_a_required_field'))
                        ->setError('typeError', __p('validation.last_name_is_a_required_field')),
                ),
            Builder::text('full_name')
                ->marginNormal()
                ->label(__p('user::phrase.full_name'))
                ->placeholder(__p('user::phrase.full_name'))
                ->returnKeyType('next')
                ->required()
                ->yup(
                    Yup::string()
                        ->required()
                        ->maxLength(Settings::get('user.maximum_length_for_full_name'))
                        ->setError('required', __p('validation.full_name_is_a_required_field'))
                        ->setError('typeError', __p('validation.full_name_is_a_required_field')),
                ),
            Builder::text('user_name')
                ->marginNormal()
                ->label(__p('core::phrase.username'))
                ->placeholder(__p('user::phrase.choose_a_username'))
                ->returnKeyType('next')
                ->autoComplete('off')
                ->required()
                ->yup(
                    Yup::string()
                        ->required()
                        ->matches(MetaFoxConstant::USERNAME_REGEX)
                        ->minLength(Settings::get('user.min_length_for_username', 5))
                        ->maxLength(Settings::get(
                            'user.max_length_for_username',
                            MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH
                        ))
                        ->setError('required', __p('validation.user_name_is_a_required_field'))
                        ->setError('typeError', __p('validation.user_name_is_a_required_field'))
                        ->setError('matches', __p('validation.please_use_only_letters_numbers_and_periods'))
                        ->setError('minLength', '${path} must be at least ${min} characters'),
                ),
            Builder::email('email')
                ->autoComplete('off')
                ->marginNormal()
                ->label(__p('core::phrase.email_address'))
                ->placeholder(__p('core::phrase.email_address'))
                ->returnKeyType('next')
                ->required()
                ->yup(
                    Yup::string()
                        ->required()
                        ->format('email')
                        ->setError('required', __p('validation.email_is_a_required_field'))
                        ->setError('typeError', __p('validation.email_is_a_required_field'))
                        ->setError('format', __p('validation.invalid_email_address')),
                ),
        );

        $this->addReenterEmailField($basic);

        $basic->addField(
            Builder::password('password')
                ->autoComplete('off')
                ->marginNormal()
                ->label(__p('user::phrase.password'))
                ->placeholder(__p('user::phrase.password'))
                ->returnKeyType('next')
                ->required()
                ->minLength(Settings::get('user.minimum_length_for_password', 8))
                ->maxLength(Settings::get('user.maximum_length_for_password', 30))
                ->yup($this->getPasswordValidate())
        );

        if (Settings::get('user.signup_repeat_password')) {
            $basic->addField(
                Builder::password('password_confirmation')
                    ->autoComplete('off')
                    ->marginNormal()
                    ->label(__p('user::phrase.confirm_password'))
                    ->placeholder(__p('user::phrase.re_enter_password'))
                    ->variant('outlined')
                    ->returnKeyType('next')
                    ->noFeedback(true)
                    ->required()
                    ->yup(
                        Yup::string()
                            ->required()
                            ->setError('required', __p('validation.password_is_a_required_field'))
                            ->setError('typeError', __p('validation.password_is_a_required_field')),
                    )
            );
        }

        if ($enableDateOfBirth) {
            $yupBirthdayField = Yup::date()
                ->nullable(true)
                ->minYear((string) $minYear, $birthdayMessage)
                ->maxYear((string) $maxYear, $birthdayMessage)
                ->setError('typeError', __p('core::phrase.invalid_date'));

            if ($isBasicFieldRequired) {
                $yupBirthdayField
                    ->required(__p('user::validation.birthday_is_a_required_field'));
            }

            $basic->addField(
                Builder::birthday('birthday')
                    ->label(__p('user::phrase.birthday'))
                    ->required($isBasicFieldRequired)
                    ->setAttribute('minDate', $minDateString)
                    ->setAttribute('maxDate', $maxDateString)
                    ->yup($yupBirthdayField)
            );
        }

        if ($enableGender) {
            $genderField = Yup::number()
                ->nullable();

            if ($isBasicFieldRequired) {
                $genderField
                    ->required(__p('user::validation.gender_is_a_required_field'));
            }

            $basic->addFields(
                Builder::gender('gender')
                    ->label(__p('user::phrase.i_am'))
                    ->required($isBasicFieldRequired)
                    ->options($this->getDefaultGenders($context))
                    ->yup($genderField),
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
                    )
            );
        }

        if ($enableLocation) {
            $yupLocationField = Yup::string()
                ->nullable();

            if ($isBasicFieldRequired) {
                $yupLocationField->nullable(false)
                    ->required(__p('user::validation.country_is_a_required_field'));
            }

            $basic->addFields(
                Builder::countryState('country_iso')
                    ->label(__p('localize::country.country'))
                    ->valueType('array')
                    ->setAttribute('countryFieldName', 'country_iso')
                    ->setAttribute('stateFieldName', 'country_state_id')
                    ->required($isBasicFieldRequired)
                    ->yup($yupLocationField),
            );
        }

        if ($enableCity) {
            $basic->addField(Builder::countryCity('country_city_code')
                ->label(__p('localize::country.city'))
                ->description(__p('localize::country.city_name'))
                ->searchEndpoint('user/city')
                ->searchParams([
                    'country' => ':country_iso',
                    'state'   => ':country_state_id',
                ]));
        }

        app('events')->dispatch('user.registration.extra_fields.build', [$basic]);

        if (Settings::get('user.new_user_terms_confirmation')) {
            $basic->addField(
                Builder::checkbox('agree')
                    ->label(__p('user::phrase.agree_field_label'))
                    ->returnKeyType('next')
                    ->required()
                    ->yup(
                        Yup::string()
                            ->required()
                            ->setError('required', __p('validation.agree_field_is_a_required_field'))
                            ->setError('typeError', __p('validation.agree_field_is_a_required_field'))
                    )
            );
        }

        $basic->addField(Captcha::getFormField('user.user_signup'));

        $footer = $this->addFooter([
            'variant' => 'horizontal',
            'style'   => [
                'justifyContent' => 'space-between',
            ],
        ]);

        $footer->addFields(
            Builder::submit()
                ->sizeLarge()
                ->marginDense()
                ->label(__p('user::phrase.create_account')),
        );
    }

    protected function addReenterEmailField(Section $basic): void
    {
        $isReenterEmail = (bool) Settings::get('user.force_user_to_reenter_email', false);

        if (!$isReenterEmail) {
            return;
        }

        $field = Builder::email('reenter_email')
            ->autoComplete('off')
            ->marginNormal()
            ->label(__p('core::phrase.reenter_email_address'))
            ->placeholder(__p('core::phrase.reenter_email_address'))
            ->returnKeyType('next')
            ->required()
            ->yup(Yup::string()
                ->required()
                ->format('email')
                ->setError('required', __p('validation.reenter_email_is_a_required_field'))
                ->setError('typeError', __p('validation.reenter_email_is_a_required_field'))
                ->setError('format', __p('validation.invalid_email_address')));

        $basic->addField($field);
    }
}
