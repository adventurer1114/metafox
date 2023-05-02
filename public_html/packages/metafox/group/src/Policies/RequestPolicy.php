<?php

namespace MetaFox\Group\Policies;

use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Group\Models\Request;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;

/**
 * Class RequestPolicy.
 * @ignore
 */
class RequestPolicy
{
    use HasPolicyTrait;
    use CheckModeratorSettingTrait;

    protected string $type = Request::ENTITY_TYPE;
}
