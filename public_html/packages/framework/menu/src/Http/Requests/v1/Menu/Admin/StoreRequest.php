<?php

namespace MetaFox\Menu\Http\Requests\v1\Menu\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Core\Http\Controllers\Api\v1\MenuAdminController::store;
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
            'name'          => ['required', 'string'],
            'resolution'    => ['string', 'required', 'in_array:web,mobile,admin'],
            'module_id'     => ['sometimes', 'string', 'nullable'],
            'resource_name' => ['sometimes', 'string', 'nullable'],
            'is_active'     => ['sometimes', 'boolean', 'nullable'],
        ];
    }
}
