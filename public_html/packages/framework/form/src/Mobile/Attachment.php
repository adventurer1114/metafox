<?php

namespace MetaFox\Form\Mobile;

use MetaFox\Core\Support\Facades\AttachmentFileType;
use MetaFox\Form\Mobile\MultiFileField as Files;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Yup\Yup;

/**
 * Class Attachment.
 * @driverName attachment
 * @driverType form-field-mobile
 */
class Attachment extends Files
{
    public function initialize(): void
    {
        parent::initialize();

        $accepts           = AttachmentFileType::getAllExtensionActive();
        $maxUploadFileSize = Settings::get('core.attachment.maximum_number_of_attachments_that_can_be_uploaded') * 1024; //kb * 1024 = byte
        $maxFiles          = Settings::get('core.attachment.maximum_number_of_attachments_that_can_be_uploaded');

        $this->setComponent(MetaFoxForm::ATTACHMENT)
            ->name('attachments')
            ->label(__p('core::phrase.attachment'))
            ->variant('standard-inlined')
            ->fullWidth()
            ->maxFiles($maxFiles)
            ->maxUploadSize($maxUploadFileSize)
            ->uploadUrl('/attachment');

        if (!empty($accepts)) {
            $this->accept('.' . implode(',.', $accepts));
        }

        $this->yup(
            Yup::array()->of(
                Yup::object()
                    ->addProperty(
                        'id',
                        Yup::number()->required(),
                    )->addProperty(
                        'file_name',
                        Yup::string()->required(),
                    )->addProperty(
                        '_destroy',
                        Yup::number()
                    )->addProperty(
                        '_new',
                        Yup::number(),
                    )->addProperty(
                        'extension',
                        Yup::string()
                            ->oneOf($accepts, __p('validation.mimes', [
                                'attribute' => 'file',
                                'values'    => implode(', ', $accepts),
                            ]))
                    ),
            )
        );
    }
}
