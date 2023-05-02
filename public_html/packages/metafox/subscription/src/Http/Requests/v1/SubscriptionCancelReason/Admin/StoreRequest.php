<?php

namespace MetaFox\Subscription\Http\Requests\v1\SubscriptionCancelReason\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Subscription\Support\Helper;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Subscription\Http\Controllers\Api\v1\SubscriptionCancelReasonAdminController::store
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
            'title' => ['required', 'string', 'between:1,' . MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __p('core::validation.name.required'),
            'title.string' => __p('core::validation.name.required'),
            'title.between' => __p('core::validation.name.length_between', [
                'min' => 1,
                'max' => MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH,
            ]),
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (!Arr::has($data, 'status')) {
            Arr::set($data, 'status', Helper::STATUS_ACTIVE);
        }

        Arr::set($data, 'is_default', false);

        return $data;
    }
}
