<?php

namespace MetaFox\Menu\Http\Requests\v1\MenuItem\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Core\Http\Controllers\Api\v1\MenuItemAdminController::store;
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
    public function rules(): array
    {
        return [
            'label'       => ['required', 'string'],
            'menu'        => ['required', 'string'],
            'module_id'   => ['required', 'string'],
            'parent_name' => ['sometimes'],
            'icon'        => ['sometimes'],
            'as'          => ['sometimes'],
            'to'          => ['sometimes'],
            'value'       => ['sometimes'],
            'ordering'    => ['sometimes'],
            'is_active'   => ['sometimes', 'numeric'],
        ];
    }

    /**
     * @param string $key
     * @param mixed  $default
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $moduleId = Arr::get($data, 'module_id');

        $package = app('core.packages')->getPackageByAlias($moduleId);

        $data['package_id'] = $package->name;

        $data = Arr::add($data, 'ordering', 1);

        return $data;
    }
}
