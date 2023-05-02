<?php

namespace MetaFox\Forum\Http\Requests\v1\ForumThread;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Platform\Rules\AllowInRule;

class StickRequest extends FormRequest
{
    public function rules(): array
    {
        $allowedValues = [false, true];

        return [
            'is_sticked' => ['required', new AllowInRule($allowedValues)],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        Arr::set($data, 'is_sticked', !$data['is_sticked']);

        return $data;
    }
}
