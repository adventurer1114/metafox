<?php

namespace MetaFox\ActivityPoint\Http\Requests\v1\PointStatistic\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Support\Browse\Browse;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\ActivityPoint\Http\Controllers\Api\v1\PointStatisticAdminController::index
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
    public function rules(): array
    {
        return [
            'q'        => ['sometimes', 'nullable'],
            'order'    => ['sometimes', 'string'],
            'order_by' => ['sometimes', new AllowInRule([Browse::SORT_TYPE_ASC, Browse::SORT_TYPE_DESC])],
        ];
    }
}
