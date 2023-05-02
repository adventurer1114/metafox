<?php

namespace MetaFox\User\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Captcha\Support\Facades\Captcha;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\User\Http\Controllers\Api\v1\UserController::loginPopupForm;
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class LoginRequest.
 */
class LoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];

        $captchaRules = Captcha::ruleOf('user.user_login');

        if (is_array($captchaRules)) {
            $rules['captcha'] = $captchaRules;
        }

        return $rules;
    }

//    public function validated($key = null, $default = null)
//    {
//        $data = parent::validated();
//
//        $data['client_id'] = config('app.api_key');
//        $data['client_secret'] = (string)config('app.api_secret');
//        $data['grant_type'] = 'password';
//        $data['scope'] = '*';
//
//        return $data;
//    }
}
