<?php

namespace MetaFox\Form;

/**
 * @property array $resource
 */
class Confirm extends AbstractForm
{
    protected function initialize(): void
    {
        $this->title($this->resource['title'] ?? __p('core::phrase.confirm'));

        $this->addFooter(
            Builder::submit()->label($this->resource['positive'] ?? 'OK'),
            Builder::cancelButton()->label($this->resource['negotive'] ?? __p('core::phrase.cancel'))
        );
    }
}
