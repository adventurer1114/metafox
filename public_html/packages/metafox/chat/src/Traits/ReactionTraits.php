<?php

namespace MetaFox\Chat\Traits;

use MetaFox\Chat\Repositories\MessageRepositoryInterface;

trait ReactionTraits
{
    protected function normalizeReactions(string|null $reactions)
    {
        if ($reactions == null) {
            return null;
        }

        $reactions = json_decode($reactions, true);
        return resolve(MessageRepositoryInterface::class)->normalizeReactions($reactions);
    }
}
