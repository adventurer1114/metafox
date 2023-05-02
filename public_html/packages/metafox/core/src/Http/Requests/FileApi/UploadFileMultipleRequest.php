<?php

namespace MetaFox\Core\Http\Requests\FileApi;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Core\Support\FileSystem\Image\Plugins\ResizeImage;
use MetaFox\Storage\Rules\MaxFileUpload;

/**
 * Class UploadFileMultipleRequest.
 */
class UploadFileMultipleRequest extends FormRequest
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
            'file.*'            => 'required', new MaxFileUpload(),
            'item_type'         => 'sometimes|string',
            'upload_type'       => 'sometimes|string|nullable',
            'thumbnail_sizes'   => 'sometimes|array',
            'thumbnail_sizes.*' => 'string',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (!isset($data['item_type'])) {
            $data['item_type'] = 'photo';
        }

        if (!isset($data['thumbnail_sizes'])) {
            $data['thumbnail_sizes'] = ResizeImage::SIZE;
        }

        return $data;
    }
}
