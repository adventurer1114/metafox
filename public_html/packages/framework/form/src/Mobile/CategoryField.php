<?php

namespace MetaFox\Form\Mobile;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Form\Constants as MetaFoxForm;

class CategoryField extends ChoiceField
{
    private ?string $repository = null;

    public function initialize(): void
    {
        $this->setComponent(MetaFoxForm::COMPONENT_SELECT)
            ->variant('standard')
            ->label(__p('core::phrase.categories'))
            ->placeholder(__p('core::phrase.select'))
            ->name('categories')
            ->valueType('array')
            ->multiple();
    }

    /**
     * @param array<int, mixed> $options
     * @return $this
     */
    public function options(array $options): self
    {
        return $this->setAttribute('options', $options);
    }

    /**
     * @param array<int, mixed> $subOptions
     * @return $this
     */
    public function subOptions(array $subOptions): self
    {
        return $this->setAttribute('subOptions', $subOptions);
    }

    /**
     * @param string $repository
     * @return CategoryField
     */
    public function setRepository(string $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @throws AuthenticationException
     */
    protected function prepare(): void
    {
        $options = $this->getAttribute('options');
        if (null === $options && $this->repository) {
            $repo = resolve($this->repository);
            $options = $repo->getCategoriesForForm();

            $this->setAttribute('options', $options);
        }
    }
}
