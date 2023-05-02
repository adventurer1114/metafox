<?php

namespace MetaFox\Mfa\Http\Requests\v1\UserAuth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Mfa\Http\Controllers\Api\v1\UserAuthController::auth
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class AuthRequest.
 */
class AuthRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        // TODO: implement validating multiple MFA services at once
        return [
            'password'          => ['required', 'string', 'exists:mfa_user_auth_tokens,value'],
            'verification_code' => ['required', 'string', 'regex:/[0-9]{6}$/'], // TODO: extend for multiple services
        ];
    }

    /**
     * @return array<string>
     */
    public function messages(): array
    {
        return [
            'verification_code.required' => __p('mfa::phrase.authenticator_code_is_a_required_field'),
            'verification_code.regex'    => __p('mfa::phrase.authenticator_code_is_invalid'),
        ];
    }
}
