<?php

namespace MetaFox\Storage\Support;

use MetaFox\Form\Html\Choice;
use MetaFox\Yup\Yup;

class SelectDiskVisibility extends Choice
{
    public function initialize(): void
    {
        parent::initialize();

        $visibilityOptions = [
            ['value' => 'public', 'label' => 'Public'],
            ['value' => 'private', 'label' => 'Private'],
        ];

        $this->required()
            ->name('visibility')
            ->options($visibilityOptions)
            ->label(__p('storage::phrase.storage_visibility'))
            ->yup(Yup::string());
    }
}
