<?php

namespace MetaFox\Contact\Http\Requests\v1\Category\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\PaginationLimitRule;
use MetaFox\Platform\Support\Helper\Pagination;

/**
 * --------------------------------------------------------------------------
 * Code Generator
 * --------------------------------------------------------------------------
 * stub: src/Http/Requests/v1/Category/Admin/IndexRequest.stub.
 */

/**
 * Class IndexRequest.
 * @ignore
 * @codeCoverageIgnore
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
            'id'        => ['sometimes', 'numeric', 'exists:contact_categories,id'],
            'page'      => ['sometimes', 'numeric', 'min:1'],
            'limit'     => ['sometimes', 'numeric', new PaginationLimitRule()],
            'parent_id' => ['sometimes', 'nullable', 'numeric', 'exists:contact_categories,id'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        if (!isset($data['id'])) {
            $data['id'] = 0;
        }

        if (!isset($data['limit'])) {
            $data['limit'] = Pagination::DEFAULT_ITEM_PER_PAGE;
        }

        return $data;
    }
}
