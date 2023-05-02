<?php

namespace MetaFox\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Rules\CaseInsensitiveUnique;
use MetaFox\Platform\Rules\UniqueSlug;

class UserUpdateRequest extends FormRequest
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
        $id            = $this->route('user');
        $usernameRegex = MetaFoxConstant::USERNAME_REGEX;

        return [
            'user_name' => [
                'required',
                'max:255',
                "regex: /$usernameRegex/",
                new UniqueSlug('user', $id),
            ],
            'email' => [
                'required',
                'max:255',
                new CaseInsensitiveUnique('users', 'email', (int) $id),

            ],
            'full_name' => 'required|max:255',
            'password'  => 'required|max:255',
        ];
    }
}
