<?php

namespace MetaFox\Hashtag\Http\Requests\v1\Hashtag\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Hashtag\Http\Controllers\Api\v1\HashtagAdminController::tagSettingForm;
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class TagSettingFormRequest.
 */
class TagSettingFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'page'  => ['sometimes', 'numeric', 'min:1'],
            'limit' => ['sometimes', 'numeric', 'min:10'],
        ];
    }
}
