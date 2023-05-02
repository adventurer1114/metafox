<?php

namespace MetaFox\Platform\Rules;

use Illuminate\Contracts\Validation\Rule;
use MetaFox\Platform\Repositories\Contracts\CategoryRepositoryInterface;

/**
 * Class CategoryRule.
 */
class CategoryRule implements Rule
{
    protected bool $isActive;
    protected CategoryRepositoryInterface $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function passes($attribute, $value): bool
    {
        $result = $this->repository->find($value);

        if ($result == null) {
            return false;
        }

        $this->isActive = $result->is_active;

        return $result->is_active;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        if (!$this->isActive) {
            return __p('core::validation.category_id.active');
        }

        return __p('core::validation.category_id.exists');
    }
}
