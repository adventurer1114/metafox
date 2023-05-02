<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Form\Html;

use MetaFox\Core\Support\FileSystem\Image\Plugins\ResizeImage;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Platform\MetaFoxFileType;

/**
 * Class SinglePhotoField.
 */
class SinglePhotoField extends File
{
    public function initialize(): void
    {
        $this->component(MetaFoxForm::SINGLE_PHOTO)
            ->name('file')
            ->label(__p('photo::phrase.photo'))
            ->fileTypes('photo')
            ->thumbnailSizes(ResizeImage::SIZE)
            ->maxUploadSize(file_type()->getFilesizePerType('photo'))
            ->uploadUrl('/file');

        $this->validation = [
            'type'       => 'object',
            'properties' => [
                'extension' => [
                    'type' => 'string',
                ],
            ],
        ];
    }

    /**
     * @return $this
     */
    public function returnBase64(bool $value = false): self
    {
        return $this->setAttribute('returnBase64', $value);
    }
}
