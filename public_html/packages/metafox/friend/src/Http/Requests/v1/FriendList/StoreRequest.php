<?php

namespace MetaFox\Friend\Http\Requests\v1\FriendList;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Facades\Settings;

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
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:' . Settings::get('friend.maximum_name_length', 64)],
        ];
    }
}
