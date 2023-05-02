<?php

namespace MetaFox\Form\Html;

use MetaFox\Core\Support\Facades\AttachmentFileType;
use MetaFox\Platform\Facades\Settings;

/**
 * Class Attachment.
 */
class Attachment extends File
{
    public function initialize(): void
    {
        $accept = AttachmentFileType::getAllExtensionActive();
        $maxUploadFileSize = Settings::get('core.attachment.maximum_file_size_each_attachment_can_be_uploaded') * 1024; //kb * 1024 = byte
        $maxFiles = Settings::get('core.attachment.maximum_number_of_attachments_that_can_be_uploaded');

        $this->validation = [
            'type' => 'array',
            'of'   => [
                'type'       => 'object',
                'properties' => [
                    'id'        => [
                        'type'     => 'number',
                        'required' => true,
                    ],
                    'file_name' => [
                        'type'     => 'string',
                        'required' => true,
                    ],
                    '_destroy'  => [
                        'type' => 'number',
                    ],
                    '_new'      => [
                        'type' => 'number',
                    ],
                    'extension' => [
                        'type'   => 'string',
                        'oneOf'  => $accept,
                        'errors' => [
                            'oneOf' => __p('validation.mimes', [
                                'attribute' => 'file',
                                'values'    => implode(', ', $accept),
                            ]),
                        ],
                    ],
                ],
            ],
        ];

        if (!empty($accept)) {
            $this->setAttribute('accept', '.' . implode(',.', $accept));
        }

        $this->component('Attachment')
            ->name('attachments')
            ->label(__p('core::phrase.attachment'))
            ->variant('outlined')
            ->fullWidth()
            ->maxNumberOfFiles($maxFiles)
            ->maxUploadSize($maxUploadFileSize)
            ->uploadUrl('/attachment')
            ->multiple(true);
    }
}
