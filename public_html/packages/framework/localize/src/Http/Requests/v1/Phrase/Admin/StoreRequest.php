<?php

namespace MetaFox\Localize\Http\Requests\v1\Phrase\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Localize\Http\Controllers\Api\v1\PhraseAdminController::store;
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
            'package_id' => ['required', 'string'],
            'locale'     => ['required', 'string'],
            'name'       => ['required', 'string'],
            'group'      => ['required', 'string'],
            'text'       => ['required', 'string'],
        ];
    }
}
