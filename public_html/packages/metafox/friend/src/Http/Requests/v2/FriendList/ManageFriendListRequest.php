<?php

namespace MetaFox\Friend\Http\Requests\v2\FriendList;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ActionFriendListRequest.
 */
class ManageFriendListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'list_id'           => ['required', 'numeric', 'exists:friend_lists,id'],
            'friend_user_ids'   => ['required', 'array'],
            'friend_user_ids.*' => ['numeric', 'exists:user_entities,id'],
        ];
    }
}
