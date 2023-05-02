<?php

namespace MetaFox\Advertise\Http\Requests\v1\Advertise;

use Illuminate\Support\Arr;
use MetaFox\Advertise\Http\Requests\v1\Advertise\Admin\StoreRequest;
use MetaFox\Platform\MetaFoxConstant;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Advertise\Http\Controllers\Api\v1\AdvertiseController::update
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends StoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $ageFrom = request()->get('age_from');

        return [
            'title'       => ['required', 'string', 'max: ' . MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH],
            'genders'     => ['nullable', 'array'],
            'genders.*'   => ['required_with:genders', 'exists:user_gender,id'],
            'age_from'    => ['nullable', 'numeric', 'min:1'],
            'age_to'      => ['nullable', 'numeric', 'min:' . (is_numeric($ageFrom) ?? 1)],
            'languages'   => ['nullable', 'array'],
            'languages.*' => ['required_with:languages', 'string', 'exists:core_languages,language_code'],
            'location'    => ['sometimes', 'nullable', 'array', 'min:0'],
        ];
    }

    protected function isAdminCP(): bool
    {
        return false;
    }

    protected function isEdit(): bool
    {
        return true;
    }
}
