<?php

namespace MetaFox\ActivityPoint\Http\Requests\v1\PointStatistic\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\ActivityPoint\Support\ActivityPoint;
use MetaFox\Platform\Rules\AllowInRule;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\ActivityPoint\Http\Controllers\Api\v1\PointStatisticAdminController::adjust
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class AdjustRequest.
 */
class AdjustRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type'        => ['required', 'integer', new AllowInRule([ActivityPoint::TYPE_SENT, ActivityPoint::TYPE_RECEIVED])],
            'amount'      => ['required', 'numeric', 'min:1'],
            'user_ids'    => ['required', 'array'],
            'user_ids.*'  => ['sometimes', 'numeric', 'exists:user_entities,id'],
            'mass_adjust' => ['sometimes', new AllowInRule([0, 1])],
        ];
    }
}
