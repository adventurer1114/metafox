<?php

namespace MetaFox\Activity\Listeners;

use MetaFox\Activity\Repositories\FeedRepositoryInterface;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;

/**
 * Class DeleteFeedListener.
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @ignore
 */
class DeleteFeedByUserAndOwnerListener
{
    public function __construct(protected FeedRepositoryInterface $repository)
    {
    }

    /**
     * @param  User     $context
     * @param  Content  $owner
     * @return void
     */
    public function handle(User $context, Content $owner): void
    {
        $this->repository->deleteFeedByUserAndOwner($context, $owner);
    }
}
