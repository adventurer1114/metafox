<?php

namespace MetaFox\Activity\Http\Requests\v1\Snooze;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Activity\Models\Snooze;
use MetaFox\Platform\Rules\PaginationLimitRule;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Activity\Http\Controllers\Api\v1\SnoozeController::index;
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
            'user_id' => ['sometimes', 'numeric', 'min:1'],
            'page'    => ['sometimes', 'numeric', 'min:1'],
            'limit'   => ['sometimes', 'numeric', new PaginationLimitRule()],
            'type'    => ['sometimes', 'in:' . implode(',', Snooze::getTypes())],
            'q'       => ['sometimes', 'string'],
        ];
    }
}
