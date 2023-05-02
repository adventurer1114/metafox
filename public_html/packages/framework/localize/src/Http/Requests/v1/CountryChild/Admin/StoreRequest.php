<?php

namespace MetaFox\Localize\Http\Requests\v1\CountryChild\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Localize\Models\CountryChild;

/**
 * Class StoreRequest.
 */
class StoreRequest extends FormRequest
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
            'state_iso'   => 'string|required',
            'state_code'  => 'int|required',
            'geo_name'    => 'int|required',
            'fips_code'   => 'string|required',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        $data['ordering'] = CountryChild::query()
                ->where('country_iso', $data['country_iso'])
                ->count() + 1;

        return $data;
    }
}
