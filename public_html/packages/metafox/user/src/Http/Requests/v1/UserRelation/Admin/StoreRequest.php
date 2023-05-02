<?php

namespace MetaFox\User\Http\Requests\v1\UserRelation\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\User\Rules\UserRelationRule;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\User\Http\Controllers\Api\v1\UserRelationAdminController::store;
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
            'locale'         => ['required', 'string', 'exists:core_languages,language_code'],
            'package_id'     => ['required', 'string', 'exists:packages,alias'],
            'group'          => ['required', 'string'],
            'phrase_var'     => ['required', 'string', new UserRelationRule()],
            'title'          => ['required', 'string'],
            'file'           => ['required', 'array'],
            'file.temp_file' => ['required_with:file', 'numeric', 'exists:storage_files,id'],
            'file.file_type' => ['required_with:file', 'string', new AllowInRule(['photo'])],
            'is_active'      => ['sometimes', 'nullable', new AllowInRule([0, 1])],
            'is_custom'      => ['sometimes', 'nullable', new AllowInRule([0, 1])],
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
