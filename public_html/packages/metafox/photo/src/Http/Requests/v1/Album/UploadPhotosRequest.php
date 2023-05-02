<?php

namespace MetaFox\Photo\Http\Requests\v1\Album;

use Illuminate\Support\Arr;
use MetaFox\Photo\Rules\UploadedAlbumItems;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Platform\Rules\PrivacyRule;

/**
 * Class UpdateRequest.
 */
class UploadPhotosRequest extends StoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'id' => ['required', 'numeric', 'exists:photo_albums,id'],
        ]);
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        Arr::set($data, 'album', Arr::get($data, 'id'));

        return $data;
    }
}
