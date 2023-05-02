<?php

namespace MetaFox\Announcement\Http\Requests\v1\Announcement\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Announcement\Models\Style;
use MetaFox\Announcement\Rules\StartDateRule;
use MetaFox\Announcement\Support\Facade\Announcement;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\User\Models\UserGender;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Announcement\Http\Controllers\Api\v1\AnnouncementAdminController::store
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
            'subject'       => ['required', 'string', 'between:3,255'],
            'intro'         => ['required', 'string', 'between:3,255'],
            'text'          => ['sometimes', 'string', 'nullable'],
            'is_active'     => ['sometimes', 'integer', new AllowInRule([0, 1])],
            'style'         => ['required', 'integer', sprintf('exists:%s,%s', Style::class, 'id')],
            'can_be_closed' => ['sometimes', 'integer', new AllowInRule([0, 1])],
            'start_date'    => ['required', 'date', new StartDateRule()],
            'roles'         => ['sometimes', 'array', 'nullable'],
            'roles.*'       => ['sometimes', 'integer', 'nullable', new AllowInRule(Announcement::getAllowedRole())],
            'country_iso'   => ['sometimes', 'string', 'nullable'],
            'gender'        => [
                'sometimes', 'numeric', new ExistIfGreaterThanZero(sprintf('exists:%s,%s', UserGender::class, 'id')), ],
            'age_from' => ['sometimes', 'numeric', 'nullable'],
            'age_to'   => ['sometimes', 'numeric', 'nullable'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data                = parent::validated($key, $default);
        $data['subject_var'] = $data['subject'];
        $data['intro_var']   = $data['intro'];
        $data['style_id']    = Arr::get($data, 'style') ?? 0;

        if (!isset($data['is_active'])) {
            $data['is_active'] = 1;
        }

        if (!isset($data['can_be_closed'])) {
            $data['can_be_closed'] = 1;
        }

        if (!isset($data['text'])) {
            $data['text'] = '';
        }

        return $data;
    }
}
