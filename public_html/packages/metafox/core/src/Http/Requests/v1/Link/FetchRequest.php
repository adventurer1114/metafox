<?php

namespace MetaFox\Core\Http\Requests\v1\Link;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Core\Http\Controllers\Api\v1\LinkController::fetch;
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class FetchRequest.
 */
class FetchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'link' => ['required'],
        ];
    }
}
