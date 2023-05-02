<?php

namespace MetaFox\Core\Http\Requests\FileApi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Core\Support\FileSystem\Image\Plugins\ResizeImage;
use MetaFox\Platform\MetaFoxFileType;
use MetaFox\Storage\Rules\MaxFileUpload;

/**
 * Class UploadFileRequest.
 */
class UploadFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file'              => ['required', new MaxFileUpload()],
            'file_type'         => ['string'],
            'item_type'         => ['sometimes', 'string'],
            'thumbnail_sizes'   => ['sometimes', 'array'],
            'thumbnail_sizes.*' => ['string'],
            'base64'            => ['sometimes', 'string'],
            'storage_id'        => ['sometimes', 'nullable', 'string', 'exists:storage_disks,name'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        $data = Arr::add($data, 'item_type', 'photo');

        $data = Arr::add($data, 'thumbnail_sizes', ResizeImage::SIZE);

        $data = Arr::add($data, 'file_type', MetaFoxFileType::PHOTO_TYPE);

        $data['file_type'] = file_type()->transformFileType($data['file_type']);

        return $data;
    }
}
