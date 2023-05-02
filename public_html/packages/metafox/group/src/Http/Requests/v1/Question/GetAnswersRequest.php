<?php

namespace MetaFox\Group\Http\Requests\v1\Question;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class GetAnswersRequest.
 */
class GetAnswersRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'group_id'   => ['required', 'numeric', 'exists:groups,id'],
            'request_id' => ['required', 'numeric', 'exists:group_requests,id'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        return parent::validated();
    }
}
