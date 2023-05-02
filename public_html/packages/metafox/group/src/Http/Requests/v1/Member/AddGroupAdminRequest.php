<?php

namespace MetaFox\Group\Http\Requests\v1\Member;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AddGroupAdminRequest.
 */
class AddGroupAdminRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'group_id'   => ['required', 'numeric', 'exists:groups,id'],
            'user_ids'   => ['required', 'array'],
            'user_ids.*' => ['required', 'numeric', 'exists:user_entities,id'],
        ];
    }
}
