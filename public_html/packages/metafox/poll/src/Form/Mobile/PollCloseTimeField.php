<?php

namespace MetaFox\Poll\Form\Mobile;

use DateTimeInterface;
use MetaFox\Form\Mobile\DateTimeField;

/**
 * @driverName pollCloseTime
 * @driverType form-field-mobile
 */
class PollCloseTimeField extends DateTimeField
{
    public const COMPONENT = 'PollCloseTime';

    public function initialize(): void
    {
        parent::initialize();

        $this->setComponent(self::COMPONENT)
            ->variant('standard')
            ->datePickerMode('datetime');
    }
}
