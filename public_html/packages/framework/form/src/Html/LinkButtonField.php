<?php

namespace MetaFox\Form\Html;

use MetaFox\Form\AbstractField;

/**
 * Class LinkButtonField.
 */
class LinkButtonField extends AbstractField
{
    public function initialize(): void
    {
        $this->component('LinkButton')
            ->color('primary')
            ->variant('text')
            ->sizeLarge()
            ->fullWidth(false);
    }

    /**
     * @param  string $color
     * @return $this
     */
    public function color(string $color): static
    {
        return $this->setAttribute('color', $color);
    }

    /**
     * @param  string $link
     * @return $this
     */
    public function link(string $link): static
    {
        return $this->setAttribute('link', $link);
    }

    public function target(string $target): static
    {
        return $this->setAttribute('target', $target);
    }
}
