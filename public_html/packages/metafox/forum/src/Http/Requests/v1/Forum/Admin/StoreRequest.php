<?php

namespace MetaFox\Forum\Http\Requests\v1\Forum\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Forum\Support\ForumSupport;
use MetaFox\Platform\Rules\AllowInRule;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:' . $this->getMaxTitleLength()],
            'description' => ['nullable', 'string'],
            'parent_id'   => ['nullable', 'numeric', 'min:0'],
            'is_closed'   => ['required', new AllowInRule([0, 1])],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __p('core::phrase.title_is_a_required_field'),
            'title.string'   => __p('core::phrase.title_is_a_required_field'),
            'title.max'      => __p('forum::validation.admincp.maximum_name_length', [
                'number' => $this->getMaxTitleLength(),
            ]),
        ];
    }

    protected function getMaxTitleLength(): int
    {
        return ForumSupport::MAX_FORUM_TITLE_LEMGTH;
    }
}
