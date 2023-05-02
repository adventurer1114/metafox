<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Form\Html;

use MetaFox\Core\Support\FileSystem\Image\Plugins\ResizeImage;

/**
 * Class SingleAudioField.
 */
class MultiAudioField extends File
{
    public function initialize(): void
    {
        $this->component('MultiAudioField')
            ->name('file')
            ->label(__p('core::web.music'))
            ->fileTypes('audio')
            ->accepts('audio/mp3')
            ->thumbnailSizes(ResizeImage::SIZE)
            ->maxUploadSize(file_type()->getFilesizePerType('audio'))
            ->uploadUrl('/file');
    }
}
