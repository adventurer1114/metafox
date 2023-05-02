<?php

namespace MetaFox\Page\Http\Requests\v1\Page;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\PaginationLimitRule;

/**
 * Class SuggestRequest.
 */
class SimilarRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'page_id'     => ['sometimes', 'numeric', 'exists:pages,id'],
            'category_id' => ['sometimes', 'numeric', 'exists:page_categories,id'],
            'limit'       => ['sometimes', 'numeric', new PaginationLimitRule()],
        ];
    }

    /**
     * validated.
     *
     * @param  mixed        $key
     * @param  mixed        $default
     * @return array<mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (!isset($data['limit'])) {
            $data['limit'] = 3;
        }

        return $data;
    }
}
