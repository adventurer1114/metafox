<?php

namespace MetaFox\Page\Http\Requests\v1\Page;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Repositories\PageCategoryRepositoryInterface;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Rules\CategoryRule;
use MetaFox\Platform\Rules\ResourceNameRule;
use MetaFox\Platform\Rules\UniqueSlug;

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends FormRequest
{
    public const MIN_TITLE_LENGTH = 3;
    public const MAX_TITLE_LENGTH = 64;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $id = (int) $this->route('page');

        return [
            'name' => [
                'sometimes', 'required', 'string', new ResourceNameRule('page'),
            ],
            'category_id' => [
                'sometimes', 'numeric', new CategoryRule(resolve(PageCategoryRepositoryInterface::class)),
            ],
            'vanity_url' => [
                'sometimes',
                'string',
                'nullable',
                new UniqueSlug(Page::ENTITY_TYPE, $id),
            ],
            'text'          => ['sometimes', 'string', 'nullable'],
            'landing_page'  => ['sometimes', 'string', 'nullable'],
            'location'      => ['sometimes', 'nullable', 'array'],
            'phone'         => ['sometimes', 'string', 'nullable'],
            'external_link' => ['sometimes', 'url', 'nullable'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (Arr::has($data, 'vanity_url')) {
            $data['profile_name'] = str_replace(MetaFoxConstant::SLUGIFY_REGEX, '_', $data['vanity_url']);
            unset($data['vanity_url']);
        }

        if (Arr::has($data, 'location')) {
            $data['location_name']      = Arr::get($data, 'location.address', null);
            $data['location_latitude']  = Arr::get($data, 'location.lat', null);
            $data['location_longitude'] = Arr::get($data, 'location.lng', null);
            unset($data['location']);
        }

        return $data;
    }

    /**
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'vanity_url.unique'  => __p('core::phrase.cannot_use_this_url_please_choose_another_one'),
            'name.required'      => __p('core::validation.name.required'),
            'category_id.exists' => __p('core::validation.category_id.exists'),
            'external_link.url'  => __p('core::validation.external_link.url'),
        ];
    }
}
