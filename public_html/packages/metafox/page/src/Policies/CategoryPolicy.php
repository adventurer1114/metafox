<?php

namespace MetaFox\Page\Policies;

use MetaFox\Page\Models\Category;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;

class CategoryPolicy
{
    use HasPolicyTrait;

    protected string $type = Category::ENTITY_TYPE;

}
