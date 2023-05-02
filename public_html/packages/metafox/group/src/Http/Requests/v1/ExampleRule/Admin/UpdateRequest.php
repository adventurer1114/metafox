<?php

namespace MetaFox\Group\Http\Requests\v1\ExampleRule\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\AllowInRule;

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'       => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'is_active'   => ['sometimes', 'numeric', new AllowInRule([0, 1])],
        ];
    }
}
