<?php

namespace MetaFox\Marketplace\Http\Requests\v1\Category\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateRequest.
 * @ignore
 * @codeCoverageIgnore
 */
class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'      => ['sometimes', 'between:3,255'],
            'name_url'  => ['sometimes', 'between:3,255'],
            'is_active' => ['sometimes', 'numeric'],
            'ordering'  => ['sometimes', 'numeric'],
            'parent_id' => ['nullable', 'numeric', 'exists:marketplace_categories,id'],
        ];
    }
}
