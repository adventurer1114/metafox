<?php

namespace MetaFox\Subscription\Http\Requests\v1\SubscriptionInvoice;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Subscription\Http\Controllers\Api\v1\SubscriptionInvoiceController::renew
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class RenewRequest.
 */
class RenewRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'payment_gateway' => ['required', 'numeric', 'exists:payment_gateway,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_gateway.required' => __p('subscription::validation.choose_one_payment_gateway_for_purchasing'),
            'payment_gateway.numeric' => __p('subscription::validation.choose_one_payment_gateway_for_purchasing'),
            'payment_gateway.exists' => __p('subscription::validation.choose_one_payment_gateway_for_purchasing'),
        ];
    }
}
