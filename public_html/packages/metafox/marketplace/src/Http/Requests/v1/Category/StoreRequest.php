<?php

namespace MetaFox\Marketplace\Http\Requests\v1\Category;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\AllowInRule;

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
            'name'      => ['required', 'string', 'between:3,255'],
            'name_url'  => ['sometimes', 'string', 'between:3,255'],
            'parent_id' => ['sometimes', 'numeric', 'exists:marketplace_categories,id'],
            'is_active' => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'ordering'  => ['sometimes', 'numeric'],
        ];
    }
}
