<?php

namespace MetaFox\Video\Http\Requests\v1\Category;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name'      => ['sometimes', 'between:3,255'],
            'name_url'  => ['sometimes', 'between:3,255'],
            'is_active' => ['sometimes', 'numeric', 'between:0,1'],
            'ordering'  => ['sometimes', 'numeric', 'min:0'],
            'parent_id' => ['nullable', 'numeric', 'exists:video_categories,id'],
        ];
    }
}
