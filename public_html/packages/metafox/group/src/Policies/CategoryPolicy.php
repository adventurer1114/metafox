<?php

namespace MetaFox\Group\Policies;

use MetaFox\Group\Models\Category as Resource;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;

/**
 * Class CategoryPolicy.
 * @ignore
 */
class CategoryPolicy
{
    use HasPolicyTrait;

    protected string $type = Resource::ENTITY_TYPE;

    // DO NOT
}
