<?php

namespace MetaFox\Friend\Support;

use MetaFox\Friend\Models\FriendRequest;
use MetaFox\Friend\Repositories\FriendRepositoryInterface;
use MetaFox\Friend\Repositories\FriendRequestRepositoryInterface;
use MetaFox\Platform\Contracts\User;

/**
 * Class Friend.
 */
class Friend
{
    public const FRIENDSHIP_CAN_ADD_FRIEND     = 0;
    public const FRIENDSHIP_IS_FRIEND          = 1;
    public const FRIENDSHIP_CONFIRM_AWAIT      = 2;
    public const FRIENDSHIP_REQUEST_SENT       = 3;
    public const FRIENDSHIP_CAN_NOT_ADD_FRIEND = 4;
    public const FRIENDSHIP_IS_OWNER           = 5;
    public const FRIENDSHIP_IS_UNKNOWN         = 6;
    public const FRIENDSHIP_IS_DENY_REQUEST    = 7;

    public const SHARED_TYPE = 'friend';

    /**
     * @return FriendRepositoryInterface
     */
    private function friendRepository(): FriendRepositoryInterface
    {
        return resolve(FriendRepositoryInterface::class);
    }

    /**
     * @return FriendRequestRepositoryInterface
     */
    private function friendRequestRepository(): FriendRequestRepositoryInterface
    {
        return resolve(FriendRequestRepositoryInterface::class);
    }

    /**
     * @param User $context
     * @param User $user
     *
     * @return int
     */
    public function getFriendship(User $context, User $user): int
    {
        //Todo: check module friend not active and add test
//        if () {
//            return self::FRIENDSHIP_IS_UNKNOWN;
//        }

        if ($context->entityId() == $user->entityId()) {
            return self::FRIENDSHIP_IS_OWNER;
        }

        if ($this->isFriend($context, $user)) {
            return self::FRIENDSHIP_IS_FRIEND;
        }

        /** @var FriendRequest $requestWait */
        $requestWait = $this->friendRequestRepository()->getRequest($context->entityId(), $user->entityId()); //current login user sent request to another user

        //another user sent request to current login user
        /** @var FriendRequest $requestSend */
        $requestSend = $this->friendRequestRepository()->getRequest($user->entityId(), $context->entityId());

        if (null != $requestWait) {
            //check deny
            if ($requestWait->is_deny) {
                if (null == $requestSend) {
                    return self::FRIENDSHIP_IS_DENY_REQUEST;
                }

                if ($requestSend->is_deny) {
                    return self::FRIENDSHIP_IS_DENY_REQUEST;
                }

                return self::FRIENDSHIP_CONFIRM_AWAIT;
            }

            return self::FRIENDSHIP_REQUEST_SENT;
        }

        if (null != $requestSend && !$requestSend->is_deny) {
            return self::FRIENDSHIP_CONFIRM_AWAIT;
        }

        if (!$context->can('sendRequest', [FriendRequest::class, $user])) {
            return self::FRIENDSHIP_CAN_NOT_ADD_FRIEND;
        }

        return self::FRIENDSHIP_CAN_ADD_FRIEND;
    }

    /**
     * @param int $userId
     *
     * @return int[]
     */
    public function getFriendIds(int $userId): array
    {
        return $this->friendRepository()->getFriendIds($userId);
    }

    public function isFriend(User $user, User $owner): bool
    {
        return $this->friendRepository()->isFriend($user->entityId(), $owner->entityId());
    }
}
