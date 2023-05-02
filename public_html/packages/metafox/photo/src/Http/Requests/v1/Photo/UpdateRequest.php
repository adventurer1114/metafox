<?php

namespace MetaFox\Photo\Http\Requests\v1\Photo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Photo\Repositories\CategoryRepositoryInterface;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Rules\CategoryRule;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Platform\Rules\PrivacyRule;
use MetaFox\Platform\Traits\Http\Request\PrivacyRequestTrait;

/**
 * Class UpdateRequest.
 */
class UpdateRequest extends FormRequest
{
    use PrivacyRequestTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'        => ['sometimes', 'string'],
            'categories'   => ['sometimes', 'array'],
            'categories.*' => ['numeric', new CategoryRule(resolve(CategoryRepositoryInterface::class))],
            'album'        => [
                'sometimes', 'nullable', 'numeric', new ExistIfGreaterThanZero('exists:photo_albums,id'),
            ],
            'tags'     => ['sometimes', 'array'],
            'tags.*'   => ['string'],
            'text'     => ['nullable', 'string'],
            'privacy'  => ['sometimes', new PrivacyRule()],
            'location' => [
                'sometimes', 'array',
            ],
            //todo location rule should be re-use able.
            'location.address' => 'string',
            'location.lat'     => 'numeric',
            'location.lng'     => 'numeric',
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        $data = $this->handlePrivacy($data);

        if (!empty($data['location'])) {
            $data['location_name']      = $data['location']['address'];
            $data['location_latitude']  = $data['location']['lat'];
            $data['location_longitude'] = $data['location']['lng'];
            unset($data['location']);
        }

        if (isset($data['album'])) {
            $data['album_id'] = $data['album'];
        }

        if (Arr::has($data, 'text')) {
            $text = Arr::get($data, 'text');

            if (null === $text) {
                Arr::set($data, 'text', MetaFoxConstant::EMPTY_STRING);
            }
        }

        return $data;
    }

    /**
     * @return array<mixed>
     */
    public function messages(): array
    {
        return [
            'title.string' => __p('core::validation.name.required'),
        ];
    }
}
