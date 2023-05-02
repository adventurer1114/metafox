<?php

namespace MetaFox\Localize\Http\Requests\v1\Country\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Localize\Http\Controllers\Api\v1\CountryAdminController::edit;
 * stub: /packages/requests/api_form_request.stub
 */

/**
 * Class EditRequest.
 */
class EditFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => ['required', 'numeric'],
        ];
    }
}
