<?php

namespace MetaFox\BackgroundStatus\Http\Requests\v1\BgsCollection\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\BackgroundStatus\Http\Controllers\Api\v1\BgsCollectionAdminController::store
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
            'title'                     => ['required', 'string', 'between:3,255'],
            'is_active'                 => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'is_default'                => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'background_temp_file'      => ['sometimes', 'array'],
            'background_temp_file.*.id' => [
                'required_if:background_temp_file.*.status,update,remove', 'numeric',
                new ExistIfGreaterThanZero('exists:storage_files,id'),
            ],
            'background_temp_file.*.status' => [
                'required_with:background_temp_file', new AllowInRule([
                    MetaFoxConstant::FILE_REMOVE_STATUS, MetaFoxConstant::FILE_UPDATE_STATUS,
                    MetaFoxConstant::FILE_CREATE_STATUS, MetaFoxConstant::FILE_NEW_STATUS,
                ]),
            ],
            'background_temp_file.*.temp_file' => [
                'required_if:background_temp_file.*.status,create', 'numeric',
                new ExistIfGreaterThanZero('exists:storage_files,id'),
            ],
            'background_temp_file.*.file_type' => [
                'required_if:background_temp_file.*.status,create', 'string', new AllowInRule(['photo']),
            ],
        ];
    }
}
