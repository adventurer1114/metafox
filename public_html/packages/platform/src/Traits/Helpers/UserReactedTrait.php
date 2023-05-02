<?php

namespace MetaFox\Platform\Traits\Helpers;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\User;

/**
 * Trait UserReactedTrait.
 */
trait UserReactedTrait
{
    /**
     * @param User        $context
     * @param Entity|null $content
     * @param string|null $typeId
     *
     * @return mixed
     */
    public function userReacted(User $context, ?Entity $content = null, ?string $typeId = null)
    {
        if ($content === null) {
            return new JsonResource([]);
        }

        return app_active('metafox/like') && $content instanceof HasTotalLike
            ? app('events')->dispatch('like.user_reacted', [$context, $content, $typeId], true)
            : new JsonResource([]);
    }

    /**
     * @param User        $context
     * @param Entity|null $content
     * @param string|null $typeId
     *
     * @return mixed
     */
    public function userMostReactions(User $context, ?Entity $content = null, ?string $typeId = null)
    {
        if ($content === null) {
            return new JsonResource([]);
        }

        return app_active('metafox/like') && $content instanceof HasTotalLike
            ? app('events')->dispatch('like.most_reactions', [$context, $content, $typeId], true)
            : new JsonResource([]);
    }
}
