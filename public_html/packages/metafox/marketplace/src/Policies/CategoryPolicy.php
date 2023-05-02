<?php

namespace MetaFox\Marketplace\Policies;

use MetaFox\Platform\Contracts\User as User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Models\User as Model;

/**
 * Class CategoryPolicy.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 */
class CategoryPolicy
{
    use HasPolicyTrait;

    protected string $type = 'marketplace_category';

    //
}
