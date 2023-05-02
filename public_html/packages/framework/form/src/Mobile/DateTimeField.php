<?php

namespace MetaFox\Form\Mobile;

use MetaFox\Form\AbstractField;
use MetaFox\Form\Constants as MetaFoxForm;

class DateTimeField extends AbstractField
{
    public function initialize(): void
    {
        $this->setComponent(MetaFoxForm::DATETIME);
    }

    public function minDateTime(mixed $min): self
    {
        return $this->setAttribute('minDateTime', $min);
    }

    public function datePickerMode(string $mode): self
    {
        return $this->setAttribute('datePickerMode', $mode);
    }

    public function displayFormat(string $format): self
    {
        return $this->setAttribute('displayFormat', $format);
    }

    public function timeFormat(int $value): self
    {
        return $this->setAttribute('timeFormat', $value);
    }
}
