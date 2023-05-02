<?php

namespace MetaFox\Subscription\Http\Requests\v1\SubscriptionCancelReason\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Subscription\Support\Helper;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Subscription\Http\Controllers\Api\v1\SubscriptionCancelReasonAdminController::index
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class IndexRequest.
 */
class IndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'statistic'      => ['sometimes', new AllowInRule([Helper::STATISTICS_ALL, Helper::STATISTICS_CUSTOM])],
            'statistic_from' => ['required_if:statistic,' . Helper::STATISTICS_CUSTOM, 'date'],
            'statistic_to'   => ['required_if:statistic,' . Helper::STATISTICS_CUSTOM, 'date', 'after:statistic_from'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        Arr::set($data, 'view', Helper::VIEW_ADMINCP);

        if (!Arr::has($data, 'statistic')) {
            Arr::set($data, 'statistic', Helper::STATISTICS_ALL);
        }

        return $data;
    }
}
