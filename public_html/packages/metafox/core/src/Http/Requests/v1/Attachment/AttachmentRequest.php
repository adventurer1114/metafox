<?php

namespace MetaFox\Core\Http\Requests\v1\Attachment;

use Illuminate\Foundation\Http\FormRequest;
use MetaFox\Core\Support\Facades\AttachmentFileType;
use MetaFox\Platform\Facades\Settings;

class AttachmentRequest extends FormRequest
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
        $fileSize = Settings::get('core.attachment.maximum_file_size_each_attachment_can_be_uploaded');

        $extensions = AttachmentFileType::getAllExtensionActive();

        $fileRule = sprintf('required|file|max:%d|mimes:%s', $fileSize, implode(',', $extensions));

        return [
            'file'        => $fileRule,
            'item_type'   => ['required', 'string'],
            'upload_type' => 'sometimes|string|nullable',
        ];
    }

    /**
     * @return array<mixed>
     */
    public function messages(): array
    {
        $messages = [];
        $extensions = AttachmentFileType::getAllExtensionActive();
        if (!empty($extensions)) {
            $messages = [
                'file.mimes' => __p('validation.mimes', ['attribute' => 'file', 'values' => implode(', ', $extensions)]),
            ];
        }

        return $messages;
    }
}
