<?php

namespace MetaFox\Group\Http\Requests\v1\Member;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Group\Models\Member;
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
            'member_type' => ['required', 'numeric', new AllowInRule([Member::MEMBER, Member::ADMIN])],
            'user_id'     => ['required', 'numeric', 'exists:user_entities,id'],
        ];
    }
}
