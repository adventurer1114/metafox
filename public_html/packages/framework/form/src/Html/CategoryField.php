<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Form\Html;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Core\Repositories\Contracts\HasCategoryFormField;
use MetaFox\Form\AbstractField;
use MetaFox\Form\Constants as MetaFoxForm;

/**
 * Class CategoryField.
 */
class CategoryField extends AbstractField
{
    /** @var mixed|null */
    protected ?string $repository = null;

    public function initialize(): void
    {
        $this->component(MetaFoxForm::COMPONENT_SELECT)
            ->variant('outlined')
            ->fullWidth()
            ->label(__p('core::phrase.categories'))
            ->name('categories')
            ->valueType('array')
            ->multiple(true);
    }

    /**
     * @return mixed
     */
    public function getRepository(): mixed
    {
        return $this->repository;
    }

    /**
     * @param mixed $repository
     * @return CategoryField
     */
    public function setRepository(mixed $repository): self
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
