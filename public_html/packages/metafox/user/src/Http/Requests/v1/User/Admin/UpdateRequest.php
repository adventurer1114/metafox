<?php

namespace MetaFox\User\Http\Requests\v1\User\Admin;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use MetaFox\Authorization\Models\Role;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Profile\Repositories\ProfileRepositoryInterface;
use MetaFox\User\Http\Requests\v1\User\UpdateRequest as UserUpdateRequest;
use MetaFox\User\Rules\AssignRoleRule;
use MetaFox\User\Support\Browse\Scopes\User\CustomFieldScope;
use MetaFox\Core\Support\Facades\CountryCity as CityFacade;
use MetaFox\Localize\Models\CountryCity;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\User\Http\Controllers\Api\v1\UserAdminController::update;
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends UserUpdateRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     * @throws AuthenticationException
     */
    public function rules(): array
    {
        $context = user();
        $userId  = $this->route('user');

        $rules = new \ArrayObject([
            'role_id'           => ['sometimes', 'integer', Rule::exists(Role::class, 'id'), new AssignRoleRule($context)],
            'user_name'         => ['required', 'string', Rule::unique('users', 'user_name')->ignore($userId)],
            'full_name'         => ['required', 'string'],
            'email'             => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'password'          => ['sometimes'],
            'birthday'          => ['sometimes', 'nullable', 'date'],
            'postal_code'       => ['sometimes', 'nullable', 'string'],
            'country_iso'       => ['sometimes', 'nullable', 'exists:core_countries,country_iso'],
            'country_state_id'  => ['sometimes', 'nullable', 'string'],
            'country_city_code' => ['sometimes', 'nullable'],
            'gender'            => [
                'sometimes', 'nullable', 'numeric', 'nullable', new ExistIfGreaterThanZero('exists:user_gender,id'),
            ],
            'custom_gender' => [
                'required_if:gender,0', 'nullable', 'numeric', new ExistIfGreaterThanZero('exists:user_gender,id'),
            ],
            'language_id'  => ['sometimes', 'string', 'nullable', 'exists:core_languages,language_code'],
            'privacy'      => ['sometimes', 'nullable', 'array'],
            'notification' => ['sometimes', 'nullable', 'array'],
            'avatar'       => ['sometimes', 'nullable', 'array'],
            'address'      => ['sometimes', 'nullable', 'string'],
        ]);

        resolve(ProfileRepositoryInterface::class)->loadEditRules($rules);

        return $rules->getArrayCopy();
    }

    /**
     * @param  string               $key
     * @param  mixed                $default
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $data = parent::validated($key, $default);

        $this->handleGender($data);
        $this->transformCountryState($data);
        $this->transformCityCode($data);
        $this->handleProfileFields($data);
        $this->handleNotification($data);

        return $data;
    }

    private function handleNotification(array &$data): void
    {
        $result = [];

        foreach (Arr::get($data, 'notification') as $channel => $types) {
            foreach ($types as $type => $values) {
                foreach ($values as $name => $value) {
                    $result[] = [
                        'channel' => $channel,
                        'value'   => $value,
                        $type     => $name,
                    ];
                }
            }
        }

        Arr::set($data, 'notification', $result);
    }

    protected function handleGender(array &$data)
    {
        $gender = Arr::get($data, 'gender') ?? 0;

        $customGender = Arr::get($data, 'custom_gender') ?? 0;

        Arr::set($data, 'gender_id', max($gender, $customGender));

        if ($gender > 0) {
            Arr::set($data, 'gender_id', $gender);
        }
    }

    protected function transformCountryState(array &$data): void
    {
        $countryStateId = Arr::get($data, 'country_state_id') ?? 0;
        if ($countryStateId) {
            return;
        }

        $countryState = Arr::get($data, 'country_state') ?? 0;
        if (is_array($countryState)) {
            $countryStateId = Arr::get($countryState, 'value') ?? 0;
        }

        Arr::set($data, 'country_state_id', $countryStateId);
    }

    protected function handleProfileFields(array &$data): void
    {
        $attributes = $this->getProfileFields();

        foreach ($attributes as $attribute) {
            Arr::set($data, 'profile.' . $attribute, Arr::get($data, $attribute));
        }

        Arr::forget($data, [...$attributes, 'gender', 'custom_gender']);
    }

    public function getProfileFields(): array
    {
        $fields = [
            'country_iso', 'country_state_id', 'country_city_code', 'language_id',
            'city_location', 'postal_code', 'gender_id', 'birthday', 'address',
        ];

        $allowCustomFields = CustomFieldScope::getAllowCustomFields();

        foreach ($allowCustomFields as $field) {
            $fields[] = $field->field_name;
        }

        return $fields;
    }

    /**
     * @param  array<string, mixed> $data
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
}
