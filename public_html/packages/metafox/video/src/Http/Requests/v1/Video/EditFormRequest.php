<?php

namespace MetaFox\Video\Http\Requests\v1\Video;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Video\Http\Controllers\Api\v1\VideoController::createForm;
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class EditFormRequest.
 */
class EditFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'owner_id' => ['sometimes', 'numeric', new ExistIfGreaterThanZero('exists:user_entities,id')],
            'id'       => ['required', 'numeric'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (empty($data['owner_id'])) {
            $data['owner_id'] = 0;
        }

        if (!empty($data['owner_id'])) {
            $data['owner_id'] = (int) $data['owner_id'];
        }

        return $data;
    }
}
