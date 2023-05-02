<?php

namespace MetaFox\Forum\Http\Requests\v1\ForumThread;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Platform\Rules\AllowInRule;

class SubscribeRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [true, false];

        return [
            'is_subscribed' => ['required', new AllowInRule($rules)],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        Arr::set($data, 'is_subscribed', !$data['is_subscribed']);

        return $data;
    }
}
