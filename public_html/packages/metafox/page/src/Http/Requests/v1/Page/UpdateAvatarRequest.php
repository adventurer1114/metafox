<?php

namespace MetaFox\Page\Http\Requests\v1\Page;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Storage\Rules\MaxFileUpload;

/**
 * Class UpdateAvatarRequest.
 */
class UpdateAvatarRequest extends FormRequest
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
            'image_crop' => ['required', 'string'], //Todo:check base64
        ];
    }
}
