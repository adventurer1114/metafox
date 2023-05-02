<?php

namespace MetaFox\Form\Mobile;

use MetaFox\Form\AbstractField;

class LocationField extends AbstractField
{
    public const COMPONENT = 'Location';
    public function initialize(): void
    {
        $this->setComponent('Location')
            ->label(__p('core::phrase.location'));
    }
}
