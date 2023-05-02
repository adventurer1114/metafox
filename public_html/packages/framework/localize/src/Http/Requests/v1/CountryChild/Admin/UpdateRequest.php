<?php

namespace MetaFox\Localize\Http\Requests\v1\CountryChild\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'country_iso' => ['required', 'string', 'exists:core_countries,country_iso'],
            'name'        => ['required', 'string'],
            'state_iso'   => 'string|sometimes|nullable',
            'state_code'  => 'int|sometimes|nullable',
            'geo_name'    => 'int|sometimes|nullable',
            'fips_code'   => 'string|sometimes|nullable',
        ];
    }
}
