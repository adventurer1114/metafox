<?php

namespace MetaFox\Blog\Http\Requests\v1\Blog;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Blog\Repositories\CategoryRepositoryInterface;
use MetaFox\Platform\Rules\AllowInRule;
use MetaFox\Platform\Rules\CategoryRule;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Platform\Rules\PrivacyRule;
use MetaFox\Platform\Rules\ResourceNameRule;
use MetaFox\Platform\Rules\ResourceTextRule;
use MetaFox\Platform\Traits\Http\Request\AttachmentRequestTrait;
use MetaFox\Platform\Traits\Http\Request\PrivacyRequestTrait;

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends FormRequest
{
    use PrivacyRequestTrait;
    use AttachmentRequestTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'title'          => ['sometimes', 'string', new ResourceNameRule('blog')],
            'categories'     => ['sometimes', 'array'],
            'categories.*'   => ['numeric', new CategoryRule(resolve(CategoryRepositoryInterface::class))],
            'file'           => ['sometimes', 'array'],
            'file.temp_file' => [
                'required_if:file.status,update', 'numeric', new ExistIfGreaterThanZero('exists:storage_files,id'),
            ],
            'file.status' => ['required_with:file', 'string', new AllowInRule(['update', 'remove'])],
            'text'        => ['sometimes', 'string', new ResourceTextRule()],
            'tags'        => ['sometimes', 'array'],
            'tags.*'      => ['string'],
            'draft'       => ['sometimes', 'numeric', new AllowInRule([0, 1])],
            'published'   => ['sometimes', 'boolean'],
            'privacy'     => ['sometimes', new PrivacyRule()],
        ];

        $rules = $this->applyAttachmentRules($rules);

        return $rules;
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $data = $this->handlePrivacy($data);

        if (isset($data['draft'])) {
            $data['is_draft'] = $data['draft'];
        }

        $data['temp_file'] = 0;
        if (isset($data['file']['temp_file'])) {
            $data['temp_file'] = $data['file']['temp_file'];
        }

        $data['remove_image'] = false;
        if (isset($data['file']['status'])) {
            $data['remove_image'] = true;
        }

        if (array_key_exists('tags', $data)) {
            $data['tags'] = parse_input()->extractResourceTopic($data['tags']);
        }

        return $data;
    }
}
