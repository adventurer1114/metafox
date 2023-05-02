<?php

namespace MetaFox\Form\Mobile;

use MetaFox\Platform\Facades\Settings;

class MultiFileField extends FileField
{
    public const COMPONENT = 'MultiFile';

    public function initialize(): void
    {
        parent::initialize();

        $maxSizes = Settings::get('storage.filesystems.max_upload_filesize', []);

        $this->setComponent(self::COMPONENT)
            ->variant('standard-inlined')
            ->maxUploadSize($maxSizes)
            ->multiple();
    }

    public function isVideoUploadAllowed(bool $allowed): self
    {
        return $this->setAttribute('isVideoUploadAllowed', $allowed);
    }
}
