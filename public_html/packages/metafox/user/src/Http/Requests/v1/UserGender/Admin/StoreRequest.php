<?php

namespace MetaFox\User\Http\Requests\v1\UserGender\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\User\Rules\CustomGenderRule;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\User\Http\Controllers\Api\v1\UserGenderAdminController::store
 * stub: /packages/requests/api_action_request.stub
 */

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
            'locale'     => ['required', 'string', 'exists:core_languages,language_code'],
            'package_id' => ['required', 'string', 'exists:packages,alias'],
            'group'      => ['required', 'string'],
            'name'       => ['required', 'string', new CustomGenderRule()],
            'text'       => ['required', 'string'],
            'is_custom'  => ['sometimes', 'numeric', 'nullable', new AllowInRule([0, 1])],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $data['phrase'] = toTranslationKey($data['package_id'], $data['group'], $data['name']);

        return $data;
    }
}
