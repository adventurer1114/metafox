<?php

namespace MetaFox\Group\Http\Requests\v1\Member;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Group\Support\Browse\Scopes\GroupMember\ViewScope;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\PaginationLimitRule;
use MetaFox\Platform\Support\Helper\Pagination;

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
            'q'                => ['sometimes', 'string', 'nullable'],
            'group_id'         => ['required', 'numeric', 'exists:groups,id'],
            'view'             => ['sometimes', 'string', new AllowInRule(ViewScope::getAllowView())],
            'page'             => ['sometimes', 'numeric', 'min:1'],
            'limit'            => ['sometimes', 'numeric', new PaginationLimitRule()],
            'not_invite_role'  => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'excluded_user_id' => ['sometimes', 'numeric', 'exists:user_entities,id'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (!isset($data['view'])) {
            $data['view'] = ViewScope::VIEW_MEMBER;
        }

        if (!isset($data['q'])) {
            $data['q'] = '';
        }

        if (!isset($data['limit'])) {
            $data['limit'] = Pagination::DEFAULT_ITEM_PER_PAGE;
        }

        if (!isset($data['not_invite_role'])) {
            $data['not_invite_role'] = 0;
        }

        if (!isset($data['excluded_user_id'])) {
            $data['excluded_user_id'] = null;
        }

        return $data;
    }
}
