<?php

namespace MetaFox\Localize\Http\Requests\v1\Language\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Localize\Http\Controllers\Api\v1\LanguageAdminController::store;
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
    public function rules()
    {
        return [
            'vendor_name'     => ['required', 'string'],
            'app_name'        => ['required', 'string'],
            'author_homepage' => ['required', 'string'],
            'author_name'     => ['required', 'string'],
            'name'            => ['required', 'string'], // language name
            'language_code'   => ['required', 'string'],
            'base_language'   => ['required', 'string'],
            'direction'       => ['required', 'string'],
        ];
    }
}
