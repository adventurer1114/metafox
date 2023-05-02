<?php

namespace MetaFox\Storage\Support;

use MetaFox\Form\Html\Text;
use MetaFox\Yup\Yup;

class SelectUniqueDiskId extends Text
{
    public function initialize(): void
    {
        parent::initialize();

        $excludes = array_keys(config('filesystems.disks'));

        $this->required()
            ->label(__p('storage::phrase.unique_disk_id'))
            ->description('This id used by developer, includes alpha numeric, "-" or "_"')
            ->yup(Yup::string()
                ->maxLength(16)
                ->notOneOf($excludes, 'The disk ID already exists.')
                ->matches('^([\w\-]+)$', 'ID includes alpha numeric, "-" or "_"')
                ->required()
                ->label('ID'));
    }
}
