<?php

namespace MetaFox\User\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Platform\Rules\PaginationLimitRule;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\User\Support\Browse\Scopes\User\SortScope;
use MetaFox\User\Support\Browse\Scopes\User\ViewScope;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\User\Http\Controllers\Api\v1\UserController::index;
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class IndexRequest.
 */
class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'q'                => ['sometimes', 'nullable', 'string'],
            'view'             => ['sometimes', 'string', new AllowInRule(ViewScope::getAllowView())],
            'gender'           => ['sometimes', 'numeric', new ExistIfGreaterThanZero('exists:user_gender,id')],
            'page'             => ['sometimes', 'numeric', 'min:1'],
            'limit'            => ['sometimes', 'numeric', new PaginationLimitRule()],
            'sort'             => ['sometimes', 'string', new AllowInRule(SortScope::getAllowSort())],
            'sort_type'        => ['sometimes', 'string', new AllowInRule(SortScope::getAllowSortType())],
            'country'          => ['sometimes', 'string', 'min:2'],
            'country_state_id' => ['sometimes', 'nullable', 'string'],
            'city'             => ['sometimes', 'nullable'],
            'city_code'        => ['sometimes', 'nullable', 'string'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $data    = Arr::add($data, 'view', ViewScope::VIEW_DEFAULT);
        $data    = Arr::add($data, 'q', '');
        $data    = Arr::add($data, 'sort', SortScope::getSortDefault());
        $data    = Arr::add($data, 'sort_type', SortScope::getDefaultSortType(Arr::get($data, 'sort')));
        $data    = Arr::add($data, 'limit', Pagination::DEFAULT_ITEM_PER_PAGE);
        $country = Arr::get($data, 'country');

        if (null === $country) {
            Arr::forget($data, ['country', 'country_state_id']);
        }

        return $data;
    }
}
