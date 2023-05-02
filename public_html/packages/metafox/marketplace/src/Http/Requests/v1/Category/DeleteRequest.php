<?php

namespace MetaFox\Marketplace\Http\Requests\v1\Category;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\AllowInRule;

class DeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'delete_type' => ['sometimes', 'numeric', new AllowInRule([1, 2])],
            'category'    => ['sometimes', 'numeric', 'exists:marketplace_categories,id'],
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (!isset($data['delete_type'])) {
            $data['delete_type'] = 1;
        }

        if ((int) $data['delete_type'] == 1 || !isset($data['category'])) {
            $data['category'] = 0;
        }

        return $data;
    }
}
