<?php

namespace MetaFox\Page\Http\Requests\v1\PageMember;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AddPageAdminRequest.
 */
class AddPageAdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'page_id'    => ['required', 'numeric', 'exists:pages,id'],
            'user_ids'   => ['required', 'array'],
            'user_ids.*' => ['required', 'numeric', 'exists:user_entities,id'],
        ];
    }
}
