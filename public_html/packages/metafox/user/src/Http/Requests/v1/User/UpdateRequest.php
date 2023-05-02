<?php

namespace MetaFox\User\Http\Requests\v1\User;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Rules\MetaFoxPasswordFormatRule;
use MetaFox\Platform\Rules\UniqueSlug;

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends FormRequest
{
    /**
     * @var MetaFoxPasswordFormatRule
     */
    private $passwordRule;

    /**
     * @return MetaFoxPasswordFormatRule
     */
    public function getPasswordRule(): MetaFoxPasswordFormatRule
    {
        if (!$this->passwordRule instanceof MetaFoxPasswordFormatRule) {
            $this->passwordRule = resolve(MetaFoxPasswordFormatRule::class);
        }

        return $this->passwordRule;
    }

    /**
     * @param  MetaFoxPasswordFormatRule $rule
     * @return void
     */
    public function setPasswordRule(MetaFoxPasswordFormatRule $rule): void
    {
        $this->passwordRule = $rule;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     * @throws AuthenticationException
     */
    public function rules(): array
    {
        $context = user();

        $usernameRegex = MetaFoxConstant::USERNAME_REGEX; //@todo: Move to site setting?

        return [
            'user_name' => [
                'sometimes',
                'string',
                new UniqueSlug('user', $context->id),
                "regex: /$usernameRegex/",
            ],
            'full_name'                 => ['sometimes', 'string'],
            'first_name'                => ['sometimes', 'string'],
            'last_name'                 => ['sometimes', 'string'],
            'email'                     => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($context)],
            'old_password'              => ['sometimes', 'current_password:api'],
            'new_password'              => ['required_with:old_password', $this->getPasswordRule()],
            'new_password_confirmation' => [
                'required_with:new_password', 'same:new_password',
            ],
            'language_id'          => ['sometimes', 'string', 'nullable', 'exists:core_languages,language_code'],
            'currency_id'          => ['sometimes', 'string', 'nullable', 'exists:core_currencies,code'],
            'phone_number'         => ['sometimes', 'string', 'nullable', 'regex:/' . MetaFoxConstant::PHONE_NUMBER_REGEX . '/'],
            'profile.language_id'  => ['sometimes', 'string', 'nullable', 'exists:core_languages,language_code'],
            'profile.currency_id'  => ['sometimes', 'string', 'nullable', 'exists:core_currencies,code'],
            'profile.phone_number' => ['sometimes', 'string', 'nullable', 'regex:/' . MetaFoxConstant::PHONE_NUMBER_REGEX . '/'],
        ];
    }

    /**
     * @param mixed $key
     * @param mixed $default
     *
     * @return array<mixed>
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        // Only allow spaces between characters
        if (isset($data['new_password'])) {
            $data['password'] = trim($data['new_password']);
        }

        if (isset($data['language_id'])) {
            $data['profile']['language_id'] = $data['language_id'];
        }

        if (isset($data['currency_id'])) {
            $data['profile']['currency_id'] = $data['currency_id'];
        }

        if (isset($data['phone_number'])) {
            $data['profile']['phone_number'] = $data['phone_number'];
        }

        return $data;
    }

    /**
     * @return array<string,string>
     */
    public function messages()
    {
        return [
            'old_password.password'          => __p('validation.current_password'),
            'new_password_confirmation.same' => __p('validation.confirmed', ['attribute' => 'new password']),
        ];
    }
}
