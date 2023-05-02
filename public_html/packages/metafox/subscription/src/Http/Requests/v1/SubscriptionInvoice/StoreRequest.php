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
 * @link \MetaFox\Subscription\Http\Controllers\Api\v1\SubscriptionInvoiceController::store
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class StoreRequest.
 */
class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id'              => ['required', 'numeric', 'exists:subscription_packages,id'],
            'renew_type'      => ['sometimes', 'string', new AllowInRule(Helper::getRenewType())],
            'payment_gateway' => ['required', 'numeric', 'exists:payment_gateway,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => __p('subscription::validation.choose_one_package_for_purchasement'),
            'id.numeric' => __p('subscription::validation.choose_one_package_for_purchasement'),
            'id.exists' => __p('subscription::validation.choose_one_package_for_purchasement'),
            'payment_gateway.required' => __p('subscription::validation.choose_one_payment_gateway_for_purchasing'),
            'payment_gateway.numeric' => __p('subscription::validation.choose_one_payment_gateway_for_purchasing'),
            'payment_gateway.exists' => __p('subscription::validation.choose_one_payment_gateway_for_purchasing'),
        ];
    }
}
