<?php

namespace MetaFox\Form\Mobile;

use MetaFox\Form\AbstractField;

class PhoneNumberField extends AbstractField
{
    public function initialize(): void
    {
        $this->setComponent('PhoneNumber');
    }
}
