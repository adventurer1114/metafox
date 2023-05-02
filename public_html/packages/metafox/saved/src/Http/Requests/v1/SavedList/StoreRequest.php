<?php

namespace MetaFox\Saved\Http\Requests\v1\SavedList;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Platform\Rules\PrivacyRule;
use MetaFox\Platform\Traits\Http\Request\PrivacyRequestTrait;
use MetaFox\Saved\Models\SavedList;

/**
 * Class StoreRequest.
 */
class StoreRequest extends FormRequest
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
            'name'    => ['required', 'string', 'max:' . SavedList::MAXIMUM_NAME_LENGTH],
            'privacy' => ['required', new PrivacyRule()],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $data = $this->handlePrivacy($data);

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function messages(): array
    {
        return [
            'name.max' => __p('saved::validation.collection_name_is_too_long_the_maximum_length_is_max', [
                'max' => SavedList::MAXIMUM_NAME_LENGTH,
            ]),
        ];
    }
}
