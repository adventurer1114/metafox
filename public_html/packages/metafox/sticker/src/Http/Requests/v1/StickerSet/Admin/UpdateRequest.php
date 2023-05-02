<?php

namespace MetaFox\Sticker\Http\Requests\v1\StickerSet\Admin;

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
 * @link \MetaFox\Sticker\Http\Controllers\Api\v1\StickerSetAdminController::update
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
    public function rules(): array
    {
        return [
            'title'     => ['sometimes', 'string'],
            'is_active' => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'file'      => ['required', 'array'],
            'file.*.id' => [
                'required_if:file.*.status,update,remove', 'numeric',
                new ExistIfGreaterThanZero('exists:storage_files,id'),
            ],
            'file.*.status' => [
                'required_with:file', new AllowInRule([
                    MetaFoxConstant::FILE_REMOVE_STATUS, MetaFoxConstant::FILE_UPDATE_STATUS,
                    MetaFoxConstant::FILE_CREATE_STATUS, MetaFoxConstant::FILE_NEW_STATUS,
                ]),
            ],
            'file.*.temp_file' => [
                'required_if:file.*.status,create', 'numeric',
                new ExistIfGreaterThanZero('exists:storage_files,id'),
            ],
        ];
    }
}
