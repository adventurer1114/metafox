<?php

namespace MetaFox\Form\Mobile;

use MetaFox\Form\AbstractField;

class SliderField extends AbstractField
{
    public function initialize(): void
    {
        $this->setComponent('Slider');
    }
}
