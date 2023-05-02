<?php

namespace MetaFox\Friend\Listeners;

use Illuminate\Support\Facades\Cache;
use MetaFox\Friend\Repositories\FriendRepositoryInterface;
use MetaFox\Friend\Support\CacheManager;

/**
 * Class GetFriendIdsListener.
 * @ignore
 * @codeCoverageIgnore
 */
class GetFriendIdsListener
{
    /** @var FriendRepositoryInterface */
    private $friendRepository;

    public function __construct(FriendRepositoryInterface $friendRepository)
    {
        $this->friendRepository = $friendRepository;
    }

    /**
     * @param int $userId
     *
     * @return array<mixed>
     */
    public function handle(int $userId): array
    {
        return Cache::remember(sprintf(CacheManager::FRIEND_LIST_IDS, $userId), 3000, function () use ($userId) {
            return $this->friendRepository->getFriendIds($userId);
        });
    }
}
