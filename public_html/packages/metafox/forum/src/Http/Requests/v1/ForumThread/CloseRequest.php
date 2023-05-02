<?php

namespace MetaFox\Forum\Http\Requests\v1\ForumThread;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Platform\Rules\AllowInRule;

class CloseRequest extends FormRequest
{
    public function rules(): array
    {
        $allowedValues = [false, true];

        return [
            'is_closed' => ['required', new AllowInRule($allowedValues)],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();

        Arr::set($data, 'is_closed', !$data['is_closed']);

        return $data;
    }
}
