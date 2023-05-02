<?php

namespace MetaFox\Group\Http\Requests\v1\Announcement;

use Illuminate\Foundation\Http\FormRequest;

/**
 * --------------------------------------------------------------------------
 *  Http request for api version v1
 * --------------------------------------------------------------------------.
 *
 * This class is used by automatic dependency injection:
 *
 * @link \MetaFox\Group\Http\Controllers\Api\v1\AnnouncementController::removeAnnouncement
 * stub: /packages/requests/api_action_request.stub
 */

/**
 * Class DeleteRequest.
 */
class DeleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'group_id'  => ['required', 'numeric', 'exists:groups,id'],
            'item_id'   => ['sometimes', 'numeric'],
            'item_type' => ['sometimes', 'string'],
        ];
    }
}
