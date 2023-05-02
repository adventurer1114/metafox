<?php

namespace MetaFox\Platform\Support\Repository\Contracts;

use Illuminate\Auth\Access\AuthorizationException;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;

/**
 * Interface HasFeature.
 */
interface HasFeature
{
    /**
     * @param User $context
     * @param int  $id
     * @param int  $feature
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function feature(User $context, int $id, int $feature): bool;

    /**
     * @param Content $model
     *
     * @return bool
     */
    public function isFeature(Content $model): bool;
}
