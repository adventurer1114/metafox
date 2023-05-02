<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Form\Html;

use MetaFox\Form\AbstractField;
use MetaFox\Form\Constants as MetaFoxForm;

/**
 * Class DividerField.
 */
class DividerField extends AbstractField
{
    public function initialize(): void
    {
        static $index = 0;
        $index        = $index + 1;
        $this->component(MetaFoxForm::FIELD_DIVIDER)
            ->name('divider_' . $index);
    }
}
