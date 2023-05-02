<?php

namespace MetaFox\Event\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;

/**
 * Class CategoryPolicy.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CategoryPolicy
{
    use HasPolicyTrait;
    use HandlesAuthorization;

    protected string $type = 'event_category';

    //
}
