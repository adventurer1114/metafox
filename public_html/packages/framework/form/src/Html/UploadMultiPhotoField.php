<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Form\Html;

use MetaFox\Platform\Facades\Settings;

/**
 * Class UploadMultiPhotoField.
 * @driverName uploadMultiMedia
 */
class UploadMultiPhotoField extends File
{
    public function initialize(): void
    {
        $this->component('UploadMultiPhoto')
            ->itemType('photo')
            ->placeholder(__p('core::web.add_photos'))
            ->name('files')
            ->maxUploadFileSize(Settings::get('storage.filesystems.max_upload_filesize'))
            ->fullWidth()
            ->label(__p('photo::phrase.photos'));
    }
}
