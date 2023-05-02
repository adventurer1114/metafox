<?php

namespace MetaFox\User\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;

class BanUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id'           => ['required', 'exists:user_entities,id'],
            'day'               => ['required', 'numeric', 'min:0'],
            'reason'            => ['sometimes'],
            'return_user_group' => ['required', 'exists:auth_roles,id'],
        ];
    }
}
