<?php

namespace MetaFox\Video\Policies;

use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\Video\Models\Category;

/**
 * Class CategoryPolicy.
 * @SuppressWarnings(PHPMD)
 * @ignore
 */
class CategoryPolicy
{
    use HasPolicyTrait;

    protected string $type = Category::class;

    // Check can view on owner.
}
