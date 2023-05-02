<?php

namespace MetaFox\Photo\Http\Requests\v1\Album;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Photo\Rules\MaximumMediaPerUpload;
use MetaFox\Photo\Rules\UploadedAlbumItems;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxFileType;
use MetaFox\Platform\Rules\ExistIfGreaterThanZero;
use MetaFox\Platform\Rules\PrivacyRule;
use MetaFox\Platform\Traits\Http\Request\PrivacyRequestTrait;

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
     * @throws AuthenticationException
     */
    public function rules(): array
    {
        $context      = user();
        $maxPerUpload = $context->getPermissionValue('photo.maximum_number_of_media_per_upload');

        return [
            'name'     => ['sometimes', 'between:3,255'],
            'text'     => ['sometimes', 'nullable', 'string'],
            'owner_id' => ['sometimes', 'numeric', new ExistIfGreaterThanZero('exists:user_entities,id')],
            'privacy'  => ['sometimes', new PrivacyRule()],
            'items'    => ['array', new UploadedAlbumItems(), new MaximumMediaPerUpload((int) $maxPerUpload)],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $data = $this->handlePrivacy($data);

        $data['description'] = '';
        if (array_key_exists('text', $data)) {
            $data['description'] = $data['text'];
        }

        if (array_key_exists('items', $data)) {
            $data['items'] = $this->handleAlbumItems($data['items']);
        }

        if (!array_key_exists('owner_id', $data)) {
            $data['owner_id'] = 0;
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>             $items
     * @return array<int, array<string, mixed>>
     */
    protected function handleAlbumItems(array $items): array
    {
        $allowOtherUpload = Settings::get('photo.photo_allow_uploading_video_to_photo_album', true);

        return collect($items)
            ->filter(function ($item) use ($allowOtherUpload) {
                return $allowOtherUpload || MetaFoxFileType::PHOTO_TYPE == $item['type'];
            })
            ->values()
            ->groupBy('status')
            ->toArray();
    }
}
