<?php

namespace MetaFox\User\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Storage\Rules\MaxFileUpload;

/**
 * Class UploadAvatarRequest.
 */
class UploadAvatarRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'image'      => ['sometimes', 'image', new MaxFileUpload()],
            'image_crop' => ['required', 'string'],
        ];
    }
}
