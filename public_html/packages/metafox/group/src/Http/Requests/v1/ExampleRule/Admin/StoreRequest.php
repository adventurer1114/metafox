<?php

namespace MetaFox\Group\Http\Requests\v1\ExampleRule\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Group\Rules\ExampleGroupRule;
use MetaFox\Platform\Rules\AllowInRule;

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
            'locale'             => ['required', 'string', 'exists:core_languages,language_code'],
            'package_id'         => ['required', 'string', 'exists:packages,alias'],
            'group'              => ['required', 'string'],
            'title_phrase'       => ['required', 'string', new ExampleGroupRule()],
            'description_phrase' => ['required', 'string', new ExampleGroupRule()],
            'title'              => ['required', 'string'],
            'description'        => ['required', 'string'],
            'is_active'          => ['sometimes', 'nullable', new AllowInRule([0, 1])],
        ];
    }

    /**
     * @param $key
     * @param $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        return parent::validated($key, $default); // TODO: Change the autogenerated stub
    }
}
