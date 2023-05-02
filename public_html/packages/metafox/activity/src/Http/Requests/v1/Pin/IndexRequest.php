<?php

namespace MetaFox\Activity\Http\Requests\v1\Pin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Activity\Http\Controllers\Api\v1\PinController::index;
 * stub: api_action_request.stub
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
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:user_entities,id'],
        ];
    }
}
