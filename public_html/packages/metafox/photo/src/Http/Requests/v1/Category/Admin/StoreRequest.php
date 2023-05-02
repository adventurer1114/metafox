<?php

namespace MetaFox\Photo\Http\Requests\v1\Category\Admin;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'name'      => ['required', 'between:3,255'],
            'name_url'  => ['sometimes', 'between:3,255'],
            'is_active' => ['sometimes', 'integer', 'min:0'],
            'ordering'  => ['sometimes', 'integer', 'min:0'],
            'parent_id' => ['sometimes', 'integer', 'min:0', 'exists:photo_categories,id'],
        ];
    }
}
