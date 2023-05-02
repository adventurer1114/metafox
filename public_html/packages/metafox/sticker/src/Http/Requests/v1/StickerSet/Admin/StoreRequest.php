<?php

namespace MetaFox\Sticker\Http\Requests\v1\StickerSet\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\AllowInRule;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Sticker\Http\Controllers\Api\v1\StickerSetAdminController::store
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
            'title'     => ['required', 'string', 'between:3,255'],
            'is_active' => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'file'      => ['required', 'file', 'mimes:zip,gif'],
        ];
    }
}
