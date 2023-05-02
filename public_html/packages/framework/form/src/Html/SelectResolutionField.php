<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Form\Html;

use MetaFox\Form\AbstractField;
use MetaFox\Form\Constants as MetaFoxForm;

/**
 * Class SelectPackageField.
 * @driverName selectResolution
 * @driverType form-field
 */
class SelectResolutionField extends AbstractField
{
    public function initialize(): void
    {
        $this->component(MetaFoxForm::COMPONENT_SELECT)
            ->variant('outlined')
            ->fullWidth()
            ->options([
                ['label' => 'Web', 'value' => 'web'],
                ['label' => 'Admin', 'value' => 'admin'],
                ['label' => 'Mobile', 'value' => 'mobile'],
            ])
            ->label(__p('core::phrase.resolution'));
    }

    /**
     * @param array<array<string,mixed> $options
     *
     * @return $this
     */
    public function options(array $options): self
    {
        return $this->setAttribute('options', $options);
    }

    public function forAdminSearchForm(): static
    {
        return $this->sizeSmall()
            ->required(false)
            ->fullWidth(false)
            ->margin('dense')
            ->minWidth(200)
            ->label(__p('core::phrase.resolution'))
            ->defaultValue('web');
    }
}
