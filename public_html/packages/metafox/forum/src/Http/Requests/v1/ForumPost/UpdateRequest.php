<?php

namespace MetaFox\Forum\Http\Requests\v1\ForumPost;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\ResourceTextRule;
use MetaFox\Platform\Traits\Http\Request\AttachmentRequestTrait;

class UpdateRequest extends FormRequest
{
    use AttachmentRequestTrait;

    public function rules(): array
    {
        $rules = [
            'text' => ['sometimes', 'string', new ResourceTextRule(true)],
        ];

        $rules = $this->applyAttachmentRules($rules);

        return $rules;
    }
}
