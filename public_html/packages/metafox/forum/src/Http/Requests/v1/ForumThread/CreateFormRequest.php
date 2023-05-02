<?php

namespace MetaFox\Forum\Http\Requests\v1\ForumThread;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Forum\Http\Controllers\Api\v1\ForumThreadController::createForm;
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class CreateFormRequest.
 */
class CreateFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'owner_id' => ['sometimes', 'numeric', new ExistIfGreaterThanZero('exists:user_entities,id')],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (!array_key_exists('owner_id', $data)) {
            Arr::set($data, 'owner_id', 0);
        }

        return $data;
    }
}
