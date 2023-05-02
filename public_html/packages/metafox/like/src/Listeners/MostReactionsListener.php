<?php

namespace MetaFox\Like\Listeners;

use MetaFox\Like\Http\Resources\v1\Reaction\ReactionItemCollection;
use MetaFox\Like\Repositories\LikeRepositoryInterface;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\User;

/**
 * Class MostReactionsListener.
 *
 * @ignore
 * @codeCoverageIgnore
 */
class MostReactionsListener
{
    /** @var LikeRepositoryInterface */
    private $repository;

    public function __construct(LikeRepositoryInterface $likeRepository)
    {
        $this->repository = $likeRepository;
    }

    /**
     * @param User         $context
     * @param HasTotalLike $content
     *
     * @return mixed
     */
    public function handle(User $context, HasTotalLike $content)
    {
        $reactions = $this->repository->getMostReactions($context, $content);

        return new ReactionItemCollection($reactions);
    }
}
