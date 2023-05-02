<?php

namespace MetaFox\Event\Http\Requests\v1\Member;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Event\Support\Facades\EventMembership;
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
            'rsvp_id' => ['required', 'numeric', new AllowInRule(EventMembership::getAllowRsvpOptions())],
            'user_id' => ['required', 'numeric', 'exists:user_entities,id'],
        ];
    }
}
