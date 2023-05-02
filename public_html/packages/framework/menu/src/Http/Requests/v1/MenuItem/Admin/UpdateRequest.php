<?php

namespace MetaFox\Menu\Http\Requests\v1\MenuItem\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Core\Http\Controllers\Api\v1\MenuItemAdminController::update;
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
            'label'       => ['required', 'string'],
            'menu'        => ['required', 'string'],
            'module_id'   => ['sometimes', 'string'],
            'parent_name' => ['sometimes'],
            'name'        => ['sometimes'],
            'icon'        => ['sometimes'],
            'as'          => ['sometimes'],
            'to'          => ['sometimes'],
            'value'       => ['sometimes'],
            'ordering'    => ['sometimes'],
            'is_active'   => ['sometimes', 'numeric'],
        ];
    }
}
