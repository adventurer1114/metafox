<?php

namespace MetaFox\User\Http\Requests\v1\User\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Rules\AllowInRule;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\User\Http\Controllers\Api\v1\UserAdminController::store;
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class ApproveRequest.
 */
class ApproveRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'approve_status' => [
                'sometimes', 'string', new AllowInRule([
                    MetaFoxConstant::STATUS_APPROVED,
                    MetaFoxConstant::STATUS_PENDING_APPROVAL,
                    MetaFoxConstant::STATUS_NOT_APPROVED,
                ]),
            ],
        ];
    }
}
