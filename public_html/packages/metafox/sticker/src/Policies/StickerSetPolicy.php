<?php

namespace MetaFox\Sticker\Policies;

use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\Sticker\Models\StickerSet;
use MetaFox\Sticker\Policies\Contracts\StickerSetPolicyInterface;

/**
 * Class StickerSetPolicy.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 */
class StickerSetPolicy implements StickerSetPolicyInterface
{
    use HasPolicyTrait;

    protected string $type = StickerSet::class;

    /**
     * @codeCoverageIgnore
     */
    public function view(User $user, StickerSet $resource): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        if (!$user->hasPermissionTo('sticker_set.create')) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     *
     * @return bool
     */
    public function update(User $user): bool
    {
        if (!$user->hasPermissionTo('sticker_set.update')) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     *
     * @return bool
     */
    public function delete(User $user): bool
    {
        if (!$user->hasPermissionTo('sticker_set.delete')) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     *
     * @return bool
     */
    public function addUserStickerSet(User $user): bool
    {
        if (!$user->hasPermissionTo('sticker_set.add_user_sticker_set')) {
            return false;
        }

        return true;
    }

    public function viewAny(User $user): bool
    {
        if (!$user->hasPermissionTo('sticker_set.view')) {
            return false;
        }

        return true;
    }
}
