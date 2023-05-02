<?php

namespace MetaFox\Photo\Http\Requests\v1\Category;

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
            'is_active' => ['sometimes', 'numeric'],
            'ordering'  => ['sometimes', 'numeric'],
            'parent_id' => ['sometimes', 'numeric', 'exists:photo_categories,id'],
        ];
    }
}
