<?php

namespace MetaFox\Profile\Repositories;

use MetaFox\Platform\Contracts\User;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface Section.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface SectionRepositoryInterface
{
    /**
     * @return array
     */
    public function getSectionForForm(): array;

    /**
     * @param  User  $user
     * @param  array $attribute
     * @return bool
     */
    public function deleteOrMoveToNewSection(User $user, array $attribute): bool;
}
