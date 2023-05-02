<?php

namespace MetaFox\Comment\Http\Requests\v1\Comment;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Comment\Traits\HandleTagFriendTrait;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends FormRequest
{
    use HandleTagFriendTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'text'     => ['sometimes', 'string', 'nullable'],
            'photo_id' => ['sometimes', new ExistIfGreaterThanZero('exists:storage_files,id')],
            'is_hide'  => ['sometimes', 'numeric', new AllowInRule([0, 1])],
        ];

        if (app_active('metafox/sticker')) {
            $rules['sticker_id'] = ['sometimes', new ExistIfGreaterThanZero('exists:stickers,id')];
        }

        return $rules;
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (array_key_exists('photo_id', $data) && null == $data['photo_id']) {
            $data['photo_id'] = 0;
        }
        if (!array_key_exists('photo_id', $data)) {
            $data['photo_id'] = null;
        }

        if (array_key_exists('sticker_id', $data) && null == $data['sticker_id']) {
            $data['sticker_id'] = 0;
        }

        if (array_key_exists('text', $data)) {
            if (empty($data['text'])) {
                $data['text'] = '';
            }

            $data['text'] = trim($data['text']);
            if (!empty($data['text'])) {
                $data['tagged_friends'] = $this->handleTaggedFriend($data);
            }
        }

        return $data;
    }
}
