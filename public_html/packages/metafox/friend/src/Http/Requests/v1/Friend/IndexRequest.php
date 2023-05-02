<?php

namespace MetaFox\Friend\Http\Requests\v1\Friend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Platform\Facades\Settings;
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
            'q'                => ['sometimes', 'nullable', 'string'],
            'view'             => ['sometimes', 'string', 'in:mutual,latest,friend,profile'],
            'list_id'          => ['sometimes', 'numeric', 'exists:friend_lists,id'],
            'user_id'          => ['required_if:view,mutual,profile', 'numeric', 'exists:user_entities,id'],
            'page'             => ['sometimes', 'numeric', 'min:1'],
            'limit'            => ['sometimes', 'numeric', new PaginationLimitRule()],
            'owner_id'         => ['sometimes', 'numeric', 'exists:user_entities,id'],
            'share_on_profile' => ['sometimes', new AllowInRule([0, 1])],
            'is_member_only'   => ['sometimes', new AllowInRule([true, false])],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if (!isset($data['limit'])) {
            $data['limit'] = $this->getDefaultLimit();
        }

        if (!isset($data['view'])) {
            $data['view'] = '';
        }

        if (!isset($data['q'])) {
            $data['q'] = '';
        }

        if (!isset($data['list_id'])) {
            $data['list_id'] = 0;
        }

        if (!Arr::has($data, 'owner_id')) {
            Arr::set($data, 'owner_id', 0);
        }

        if (!Arr::has($data, 'user_id')) {
            Arr::set($data, 'user_id', 0);
        }

        if (Arr::has($data, 'share_on_profile')) {
            Arr::set($data, 'share_on_profile', (bool) Arr::get($data, 'share_on_profile'));
        }

        return $data;
    }

    protected function getDefaultLimit(): int
    {
        return Pagination::DEFAULT_ITEM_PER_PAGE_SPECIAL_CASE;
    }
}
