<?php

namespace MetaFox\Marketplace\Http\Requests\v1\Invite;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\PaginationLimitRule;
use MetaFox\Platform\Support\Helper\Pagination;

class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        //todo: most of these below attributes is cloned from v4, highly change to adapt v5
        return [
            'listing_id' => ['required', 'numeric', 'exists:marketplace_listings,id'],
            'visited'    => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'page'       => ['sometimes', 'numeric', 'min:1'],
            'limit'      => ['sometimes', 'numeric', new PaginationLimitRule()],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (!isset($data['limit'])) {
            $data['limit'] = Pagination::DEFAULT_ITEM_PER_PAGE;
        }

        if (!isset($data['visited'])) {
            $data['visited'] = 0;
        }

        return $data;
    }
}
