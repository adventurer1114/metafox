<?php

namespace MetaFox\User\Http\Requests\v1\UserRelation\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\AllowInRule;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\User\Http\Controllers\Api\v1\UserRelationAdminController::update;
 * stub: /packages/requests/api_action_request.stub
 */

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
    public function rules()
    {
        return [
            'locale'         => ['sometimes', 'string', 'exists:core_languages,language_code'],
            'title'          => ['sometimes', 'string'],
            'file'           => ['required', 'array'],
            'file.temp_file' => ['required_with:file', 'numeric', 'exists:storage_files,id'],
            'is_active'      => ['sometimes', 'numeric', new AllowInRule([0, 1])],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $data['temp_file'] = 0;
        if (isset($data['file']['temp_file'])) {
            $data['temp_file'] = $data['file']['temp_file'];
        }

        return $data;
    }
}
