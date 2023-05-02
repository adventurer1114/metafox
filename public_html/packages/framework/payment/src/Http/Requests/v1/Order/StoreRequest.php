<?php

namespace MetaFox\Payment\Http\Requests\v1\Order;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Payment\Http\Controllers\Api\v1\OrderController::store
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
    public function rules()
    {
        return [
            'gateway_id' => ['required', 'numeric', 'exists:payment_gateways,id'],
            'item_id'    => ['required', 'numeric'],
            'item_type'  => ['required', 'string'],
        ];
    }
}
