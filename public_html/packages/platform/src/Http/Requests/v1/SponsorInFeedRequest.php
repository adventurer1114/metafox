<?php

namespace MetaFox\Platform\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SponsorInFeedRequest.
 */
class SponsorInFeedRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sponsor' => ['required', 'numeric', 'in:0,1'],
        ];
    }
}
