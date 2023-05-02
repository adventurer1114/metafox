<?php

namespace MetaFox\User\Http\Requests\v1\User;

use ArrayObject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Core\Support\Facades\CountryCity as CityFacade;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Form\RelationTrait;
use MetaFox\Localize\Models\CountryCity;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Profile\Repositories\ProfileRepositoryInterface;

/**
 * Class UpdateProfileRequest.
 */
class UpdateProfileRequest extends FormRequest
{
    use RelationTrait;

    /**
     * Get the validation rules that apply to the request.$user.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $relations = MetaFoxForm::getRelations();

        $allowedRelations = [0, ...array_keys($relations)];

        $isBasicFieldRequired = Settings::get('user.require_basic_field', false);

        $isRelationshipStatusEnabled = Settings::get('user.enable_relationship_status', false);

        $requireBasicField = $isBasicFieldRequired ? ['required'] : ['sometimes', 'nullable'];

        $rules = new ArrayObject([
            'birthday'            => [...$requireBasicField, 'date'],
            'postal_code'         => ['sometimes', 'nullable', 'string'],
            'country_iso'         => [...$requireBasicField, 'string', 'min:2'],
            'country_state_id'    => ['sometimes', 'nullable', 'string'],
            'country_state'       => ['sometimes', 'nullable', 'array'],
            'country_state.value' => ['sometimes', 'nullable', 'string'],
            'country_city_code'   => ['sometimes', 'nullable'],
            'gender'              => [
                ...$requireBasicField, 'numeric', 'nullable', new ExistIfGreaterThanZero('exists:user_gender,id'),
            ],
            'custom_gender' => [
                'required_if:gender,0', 'nullable', 'numeric', new ExistIfGreaterThanZero('exists:user_gender,id'),
            ],
            'relation' => [
                'sometimes', 'nullable', 'numeric', new ExistIfGreaterThanZero('exists:user_relation,id'),
            ],
            'relation_with'    => ['sometimes', 'nullable', 'array'],
            'relation_with.id' => ['sometimes', 'numeric', new ExistIfGreaterThanZero('exists:user_entities,id')],
            'bio'              => ['sometimes', 'string', 'nullable'],
            'about_me'         => ['sometimes', 'string', 'nullable'],
            'interest'         => ['sometimes', 'string', 'nullable'],
            'hobbies'          => ['sometimes', 'string', 'nullable'],
            'address'          => ['sometimes', 'string', 'nullable'],
        ]);

        if ($isRelationshipStatusEnabled) {
            $rules['relation'] = [
                'sometimes', 'nullable', 'numeric', new ExistIfGreaterThanZero('exists:user_relation,id'),
            ];
            $rules['relation_with']      = ['sometimes', 'array', 'nullable'];
            $rules['relation_with.*.id'] = [
                'sometimes', 'numeric', new ExistIfGreaterThanZero('exists:user_entities,id'),
            ];
        }

        resolve(ProfileRepositoryInterface::class)->loadEditRules($rules);

        return $rules->getArrayCopy();
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if (!array_key_exists('relation_with', $data)) {
            $data['relation_with'] = 0;
        }

        if (array_key_exists('relation_with', $data)) {
            $relationWith = Arr::get($data, 'relation_with');

            if (is_array($relationWith)) {
                $relationWith = Arr::get($relationWith, 'id');
            }

            Arr::set($data, 'relation_with', $relationWith);
        }

        $gender = Arr::get($data, 'gender') ?? 0;

        $customGender = Arr::get($data, 'custom_gender') ?? 0;

        $data['gender_id'] = max($gender, $customGender);

        if ($gender > 0) {
            $data['gender_id'] = $gender;
        }

        if (!isset($data['relation'])) {
            $data['relation'] = 0;
        }

        $withRelations = $this->getWithRelations();

        if (!in_array(Arr::get($data, 'relation'), $withRelations)) {
            Arr::set($data, 'relation_with', 0);
        }

        $this->transformCityCode($data);
        $this->transformCountryState($data);

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        return [
            'country_iso.required'      => __p('user::phrase.country_is_a_required_field'),
            'birthday.required'         => __p('user::phrase.birthday_is_a_required_field'),
            'gender.required'           => __p('user::phrase.gender_is_a_required_field'),
            'relation.numeric'          => __p('user::phrase.relationship_status_is_a_required_field'),
            'custom_gender.required_if' => __p('user::validation.custom_gender_field_is_a_required_field'),
        ];
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

        $data['country_state_id'] = $countryStateId;
    }
}
