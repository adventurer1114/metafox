<?php

namespace MetaFox\Like\Http\Resources\v1;

use MetaFox\Like\Http\Resources\v1\Reaction\ReactionItemCollection;
use MetaFox\Like\Repositories\ReactionRepositoryInterface;

class PackageSetting
{
    public function getMobileSettings(): array
    {
        return [
            'reaction_list' => $this->getReactions(),
        ];
    }

    public function getWebSettings(): array
    {
        return [
            'reaction_list' => $this->getReactions(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getReactions(): array
    {
        $repository = resolve(ReactionRepositoryInterface::class);
        $reactions = $repository->getReactionsForConfig();

        return (new ReactionItemCollection($reactions))->toArray(null);
    }
}
