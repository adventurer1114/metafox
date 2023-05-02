<?php

namespace MetaFox\Comment\Http\Requests\v1\Comment;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Captcha\Support\Facades\Captcha;
use MetaFox\Comment\Exceptions\ValidateCommentException;
use MetaFox\Comment\Traits\HandleTagFriendTrait;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;

/**
 * Class StoreRequest.
 */
class StoreRequest extends FormRequest
{
    use HandleTagFriendTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $existRule = new ExistIfGreaterThanZero(
            'exists:comments,id',
            __p('comment::validation.the_post_has_been_removed_desc')
        );

        $rules = [
            'item_id'   => ['required', 'numeric'],
            'item_type' => ['required', 'string'],
            'text'      => ['required_without:photo_id', 'string', 'nullable'],
            'parent_id' => ['sometimes', 'numeric', $existRule],
            'photo_id'  => ['sometimes', 'nullable', 'numeric', 'exists:storage_files,id'],
        ];

        if (app_active('metafox/sticker')) {
            $rules['sticker_id'] = ['sometimes', 'nullable', 'numeric', 'exists:stickers,id'];
            $rules['text']       = ['required_without_all:photo_id,sticker_id', 'nullable', 'string'];
        }

        $captchaRules = Captcha::ruleOf($this->getCaptchaRuleName());

        if (is_array($captchaRules)) {
            $rules['captcha'] = $captchaRules;
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        $message = $validator->getMessageBag()->first() ?? '';
        throw (new ValidateCommentException($message));
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (!isset($data['parent_id'])) {
            $data['parent_id'] = 0;
        }

        if (!isset($data['photo_id'])) {
            $data['photo_id'] = 0;
        }

        if (!isset($data['text'])) {
            $data['text'] = '';
        }

        $data['text'] = trim($data['text']);

        if (app_active('sticker')) {
            if (!isset($data['sticker_id'])) {
                $data['sticker_id'] = 0;
            }
        }

        $text = Arr::get($data, 'text');

        if (null !== $text) {
            $data['tagged_friends'] = $this->handleTaggedFriend($data);
        }

        return $data;
    }

    protected function getCaptchaRuleName(): string
    {
        return 'comment.create_comment';
    }
}
