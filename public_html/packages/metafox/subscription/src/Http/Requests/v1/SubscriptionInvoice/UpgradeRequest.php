<?php

namespace MetaFox\Subscription\Http\Requests\v1\SubscriptionInvoice;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Subscription\Support\Helper;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Subscription\Http\Controllers\Api\v1\SubscriptionInvoiceController::upgrade
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class UpgradeRequest.
 */
class UpgradeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'renew_type' => ['sometimes', new AllowInRule(Helper::getRenewType())],
            'payment_gateway' => ['required', 'numeric', 'exists:payment_gateway,id'],
            'action_type' => ['required', new AllowInRule(Helper::getUpgradeType())],
        ];
    }

    public function messages()
    {
        return [
            'payment_gateway.required' => __p('subscription::validation.choose_one_payment_gateway_for_purchasing'),
            'payment_gateway.exists'   => __p('subscription::validation.choose_one_payment_gateway_for_purchasing'),
        ];
    }
}
