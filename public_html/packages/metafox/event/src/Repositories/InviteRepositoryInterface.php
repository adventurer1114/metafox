<?php

namespace MetaFox\Event\Repositories;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use MetaFox\Event\Models\Event;
use MetaFox\Event\Models\Invite;
use MetaFox\Platform\Contracts\User;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Interface InviteRepositoryInterface.
 * @mixin BaseRepository
 */
interface InviteRepositoryInterface
{
    /**
     * @param User  $context
     * @param int   $eventId
     * @param int[] $userIds
     *
     * @return void
     * @throws AuthorizationException
     * @throws ValidatorException
     */
    public function inviteFriends(User $context, int $eventId, array $userIds): void;

    /**
     * @param int  $eventId
     * @param User $user
     * @param bool $notInviteAgain
     *
     * @return bool
     */
    public function handleLeaveEvent(int $eventId, User $user, bool $notInviteAgain): bool;

    /**
     * @param int  $eventId
     * @param User $user
     *
     * @return void
     */
    public function handleJoinEvent(int $eventId, User $user): void;

    /**
     * @param User $context
     * @param int  $eventId
     * @param int  $userId
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function deleteEventInvite(User $context, int $eventId, int $userId): bool;

    /**
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @return Paginator
     * @throws AuthorizationException
     */
    public function viewInvites(User $context, array $attributes): Paginator;

    /**
     * @param int  $eventId
     * @param User $user
     *
     * @return Invite|null
     */
    public function getInvite(int $eventId, User $user): ?Invite;

    /**
     * @param Event $event
     * @param User  $user
     *
     * @return bool
     * @throws ValidatorException
     */
    public function acceptInvite(Event $event, User $user): bool;

    /**
     * @param Event $event
     * @param User  $user
     *
     * @return bool
     */
    public function declineInvite(Event $event, User $user): bool;

    /**
     * @param int  $eventId
     * @param User $user
     *
     * @return Invite|null
     */
    public function getPendingInvite(int $eventId, User $user): ?Invite;

    /**
     * @param Event $event
     */
    public function getPendingInvites(Event $event): Collection;

    /**
     * @param  int  $userId
     * @return void
     */
    public function deleteInvited(int $ownerId): void;

    /**
     * @param  int  $userId
     * @return void
     */
    public function deleteInvite(int $userId): void;

    /**
     * @param  Invite $invite
     * @return void
     */
    public function deleteNotification(Invite $invite): void;
}
