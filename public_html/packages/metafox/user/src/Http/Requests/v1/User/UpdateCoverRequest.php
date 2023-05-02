<?php

namespace MetaFox\User\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Storage\Rules\MaxFileUpload;

/**
 * Class UpdateCoverRequest.
 */
class UpdateCoverRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'image'    => ['sometimes', 'image', new MaxFileUpload()],
            'position' => ['sometimes', 'string'],
        ];
    }
}
