<?php

namespace MetaFox\Form\Mobile;

use MetaFox\Form\AbstractField;
use MetaFox\Form\Constants as MetaFoxForm;

class ChoiceField extends AbstractField
{
    public function initialize(): void
    {
        $this->setComponent(MetaFoxForm::COMPONENT_SELECT);
    }

    public function renderWithoutOptions(): self
    {
        return $this->setAttribute('showWithoutOptions', true);
    }

    /**
     * @param array<array<string,mixed>> $data
     *
     * @return $this
     */
    public function options(array $data): self
    {
        return $this->setAttribute('options', $data);
    }

    /**
     * @param  string $name
     * @return $this
     */
    public function relatedFieldName(string $name): self
    {
        return $this->setAttribute('relatedFieldName', $name);
    }

    /**
     * @param  array $options
     * @return $this
     */
    public function optionRelatedMapping(array $options): self
    {
        return $this->setAttribute('optionRelatedMapping', $options);
    }

    public function enableSearch(bool $flag = true): static
    {
        return $this->setAttribute('enable_search', $flag);
    }
}
