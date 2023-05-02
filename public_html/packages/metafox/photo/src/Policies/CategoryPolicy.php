<?php

namespace MetaFox\Photo\Policies;

use MetaFox\Photo\Models\Category;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;

class CategoryPolicy
{
    use HasPolicyTrait;

    protected string $type = Category::class;

    // Check can view on
}
