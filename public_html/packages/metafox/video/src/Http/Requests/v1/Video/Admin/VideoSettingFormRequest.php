<?php

namespace MetaFox\Video\Http\Requests\v1\Video\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Video\Http\Controllers\Api\v1\VideoAdminController::videoSettingForm;
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class VideoSettingFormRequest.
 */
class VideoSettingFormRequest extends FormRequest
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
