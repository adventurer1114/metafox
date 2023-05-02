<?php

namespace MetaFox\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_name' => 'required|unique:users|max:255',
            'email'     => 'required|unique:users|email|max:255',
            'full_name' => 'required|max:255',
            'password'  => 'required|max:255',
        ];
    }
}
