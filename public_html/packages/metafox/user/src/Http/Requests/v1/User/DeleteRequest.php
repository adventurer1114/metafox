<?php

namespace MetaFox\User\Http\Requests\v1\User;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Platform\Rules\MetaFoxPasswordFormatRule;
use MetaFox\Platform\Rules\UniqueSlug;

/**
 * Class DeleteRequest.
 */
class DeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'reason_id' => ['sometimes', 'numeric', new ExistIfGreaterThanZero('exists:user_delete_reasons,id')],
            'feedback'  => ['sometimes', 'string', 'nullable'],
            'password'  => ['required', 'string'],
        ];
    }

    /**
     * @throws AuthenticationException
     */
    public function validated($key = null, $default = null)
    {
        $context  = user();
        $data     = parent::validated($key, $default);
        $password = Arr::get($data, 'password');

        if (!Hash::check($password, $context->getAuthPassword())) {
            abort(422, __p('user::phrase.password_is_not_correct'));
        }

        if ($context->hasSuperAdminRole()) {
            abort(401, __p('user::phrase.password_is_not_correct'));
        }

        return $data;
    }
}
