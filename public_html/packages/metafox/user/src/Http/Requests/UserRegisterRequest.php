<?php

namespace MetaFox\User\Http\Requests;

use ArrayObject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use MetaFox\Captcha\Support\Facades\Captcha;
use MetaFox\Core\Support\Facades\CountryCity as CityFacade;
use MetaFox\Core\Support\Facades\Language;
use MetaFox\Localize\Models\CountryCity;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Rules\CaseInsensitiveUnique;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Platform\Rules\MetaFoxPasswordFormatRule;
use MetaFox\Platform\Rules\UniqueSlug;

/**
 * Class UserRegisterRequest.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserRegisterRequest extends FormRequest
{
    public const ACTION_CAPTCHA_NAME = 'user.user_signup';

    /**
     * @var MetaFoxPasswordFormatRule
     */
    private $passwordRule;

    /**
     * @return mixed
     */
    public function getPasswordRule()
    {
        if (!$this->passwordRule instanceof MetaFoxPasswordFormatRule) {
            $this->passwordRule = resolve(MetaFoxPasswordFormatRule::class);
        }

        return $this->passwordRule;
    }

    /**
     * @param mixed $passwordRule
     */
    public function setPasswordRule($passwordRule): void
    {
        $this->passwordRule = $passwordRule;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $usernameRegex        = MetaFoxConstant::USERNAME_REGEX; //@todo: Move to site setting?
        $isBasicFieldRequired = Settings::get('user.require_basic_field', false);
        $requireBasicField    = $isBasicFieldRequired ? ['required'] : ['sometimes', 'nullable'];

        Log::channel('dev')->info('get rules');

        $rules = new ArrayObject([
            'user_name' => [
                'string',
                'required',
                new UniqueSlug('user'),
                "regex: /$usernameRegex/",
            ],
            'full_name'  => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'last_name'  => ['required', 'string'],
            'email'      => [
                'required',
                'string',
                'email',
                new CaseInsensitiveUnique('users', 'email'),
            ],
            'password' => ['required', 'string', $this->getPasswordRule()],
        ]);

        if (Settings::get('user.signup_repeat_password')) {
            $rules['password_confirmation'] = ['required_with:password', 'string', 'same:password'];
        }

        if (Settings::get('user.force_user_to_reenter_email', false)) {
            $rules['reenter_email'] = ['required_with:email', 'string', 'same:email'];
        }

        if (Settings::get('user.new_user_terms_confirmation', true)) {
            $rules['agree'] = ['required', 'accepted'];
        }

        if (Settings::get('user.enable_date_of_birth', false)) {
            $rules['birthday'] = [...$requireBasicField, 'date'];
        }

        if (Settings::get('user.enable_gender', false)) {
            $rules['gender'] = [
                ...$requireBasicField, 'numeric', 'nullable', new ExistIfGreaterThanZero('exists:user_gender,id'),
            ];
            $rules['custom_gender'] = [
                'required_if:gender,0', 'nullable', 'numeric', new ExistIfGreaterThanZero('exists:user_gender,id'),
            ];
        }

        if (Settings::get('user.enable_location', false)) {
            $rules['country_iso']         = [...$requireBasicField, 'string', 'min:2'];
            $rules['country_state_id']    = ['sometimes', 'nullable', 'string'];
            $rules['country_state']       = ['sometimes', 'nullable', 'array'];
            $rules['country_state.value'] = ['sometimes', 'nullable', 'string'];
        }

        if (Settings::get('user.enable_city', false)) {
            $rules['country_city_code'] = ['sometimes', 'nullable'];
        }

        $rules['captcha'] = Captcha::ruleOf(self::ACTION_CAPTCHA_NAME);

        app('events')->dispatch('user.registration.extra_field.rules', [$rules]);

        Log::channel('dev')->debug('validation_rules', $rules->getArrayCopy());

        return $rules->getArrayCopy();
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        // Only allow spaces between characters
        $data['password']       = trim($data['password']);
        $data['approve_status'] = MetaFoxConstant::STATUS_APPROVED;

        $this->transformCountryState($data);
        $this->transformCityCode($data);
        $this->transformUserLanguage($data);

        $this->handleBasicProfileFields($data);

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        $messages = [
            'agree.accepted'             => __p('validation.required', ['attribute' => 'agree']),
            'password.required'          => __p('validation.password_field_validation_required'),
            'password_confirmation.same' => __p('validation.the_password_confirmation_is_not_matched'),
            'reenter_email.same'         => __p('validation.the_reenter_email_is_not_matched'),
            'custom_gender.required_if'  => __p('validation.the_custom_gender_field_is_a_required_field'),
        ];

        $extraMessages = app('events')->dispatch('user.registration.extra_field.rule_messages');

        if (is_array($extraMessages)) {
            foreach ($extraMessages as $extraMessage) {
                if (is_array($extraMessage)) {
                    $messages = array_merge($messages, $extraMessage);
                }
            }
        }

        return $messages;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return void
     */
    protected function transformCityCode(array &$data): void
    {
        $cityCode = Arr::get($data, 'country_city_code') ?? 0;

        if (is_array($cityCode)) {
            $cityCode = Arr::get($cityCode, 'value') ?? 0;
        }

        $data['country_city_code'] = $cityCode;
        $city                      = CityFacade::getCity($cityCode);
        $data['city_location']     = $city instanceof CountryCity ? $city->name : '';
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return void
     */
    protected function handleBasicProfileFields(array &$data): void
    {
        $this->handleGenderField($data);
        $fields = $this->getBasicProfileFields();

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $data['profile'][$field] = $data[$field];
                unset($data[$field]);
            }
        }
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return void
     */
    public function handleGenderField(array &$data): void
    {
        $gender = Arr::get($data, 'gender') ?? 0;

        $customGender = Arr::get($data, 'custom_gender') ?? 0;

        $data['gender_id'] = max($gender, $customGender);

        if ($gender > 0) {
            $data['gender_id'] = $gender;
        }
    }

    /**
     * @return array<string>
     */
    protected function getBasicProfileFields(): array
    {
        return [
            'country_state_id', 'country_city_code', 'country_iso',
            'birthday', 'city_location', 'gender_id', 'language_id',
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return void
     */
    protected function transformCountryState(mixed $data)
    {
        $countryStateId = Arr::get($data, 'country_state_id') ?? 0;
        if ($countryStateId) {
            return;
        }

        $countryState = Arr::get($data, 'country_state') ?? 0;
        if (is_array($countryState)) {
            $countryStateId = Arr::get($countryState, 'value') ?? 0;
        }

        $data['country_state_id'] = $countryStateId;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return void
     */
    protected function transformUserLanguage(array &$data)
    {
        $locale           = Arr::get($data, 'language_id', App::getLocale());
        $availableLocales = Language::availableLocales();

        if (empty($locale) || !in_array($locale, $availableLocales)) {
            $locale = Language::getDefaultLocaleId();
        }

        $data['language_id'] = $locale;
    }
}
