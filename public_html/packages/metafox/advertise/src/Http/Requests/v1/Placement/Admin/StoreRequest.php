<?php

namespace MetaFox\Advertise\Http\Requests\v1\Placement\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Advertise\Support\Facades\Support;
use MetaFox\Authorization\Repositories\Contracts\RoleRepositoryInterface;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\ResourceTextRule;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Advertise\Http\Controllers\Api\v1\PlacementAdminController::store
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
        $typeOptions = array_column(Support::getPlacementTypes(), 'value');

        $rules = [
            'title'                => ['required', 'string', 'max:' . MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH],
            'text'                 => ['required', 'string', new ResourceTextRule()],
            'placement_type'       => ['required', new AllowInRule($typeOptions)],
            'allowed_user_roles'   => ['nullable', 'array'],
            'allowed_user_roles.*' => ['required_with:allowed_user_roles', 'numeric', 'exists:auth_roles,id'],
            'is_active'            => ['required', new AllowInRule([0, 1])],
        ];

        $rules = $this->addPriceRules($rules);

        return $rules;
    }

    protected function addPriceRules(array $rules): array
    {
        $currencies = app('currency')->getActiveOptions();

        $name = 'price';

        foreach ($currencies as $currency) {
            $rules[$name . '_' . $currency['value']] = ['required', 'numeric', 'min:0'];
        }

        return $rules;
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $data = $this->prepareRoles($data);

        $data = $this->preparePrices($data);

        return $data;
    }

    protected function preparePrices(array $data): array
    {
        $currencies = app('currency')->getActiveOptions();

        $name = 'price_';

        $price = [];

        foreach ($currencies as $currency) {
            $key = $name . $currency['value'];

            if (!Arr::has($data, $key)) {
                continue;
            }

            Arr::set($price, $currency['value'], Arr::get($data, $key, 0));

            unset($data[$key]);
        }

        Arr::set($data, 'price', $price);

        return $data;
    }

    protected function prepareRoles(array $data): array
    {
        $roleIds = Arr::get($data, 'allowed_user_roles');

        if (!is_array($roleIds) || !count($roleIds)) {
            Arr::set($data, 'allowed_user_roles', null);

            return $data;
        }

        $repository = resolve(RoleRepositoryInterface::class);

        $roles = $repository->getRoleOptions();

        $availableRoleIds = array_column($roles, 'value');

        $disallowedRoleIds = Support::getDisallowedUserRoleOptions();

        $availableRoleIds = array_filter($availableRoleIds, function ($availableRoleId) use ($disallowedRoleIds) {
            return !in_array($availableRoleId, $disallowedRoleIds);
        });

        $diff = array_diff($availableRoleIds, $roleIds);

        if (count($diff)) {
            return $data;
        }

        /*
         * Null means all available roles
         */
        Arr::set($data, 'allowed_user_roles', null);

        return $data;
    }
}
