<?php

namespace MetaFox\Event\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;
use MetaFox\Event\Models\Event;
use MetaFox\Event\Models\Invite;
use MetaFox\Event\Models\Member;
use MetaFox\Event\Policies\EventPolicy;
use MetaFox\Event\Repositories\EventRepositoryInterface;
use MetaFox\Event\Repositories\InviteRepositoryInterface;
use MetaFox\Event\Repositories\MemberRepositoryInterface;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Traits\Helpers\IsFriendTrait;
use MetaFox\User\Models\UserEntity as UserEntityModel;
use MetaFox\User\Support\Facades\UserEntity;

class InviteRepository extends AbstractRepository implements InviteRepositoryInterface
{
    use IsFriendTrait;

    public function model(): string
    {
        return Invite::class;
    }

    /**
     * @return EventRepositoryInterface
     */
    private function eventRepository(): EventRepositoryInterface
    {
        return resolve(EventRepositoryInterface::class);
    }

    /**
     * @return MemberRepositoryInterface
     */
    private function eventMemberRepository(): MemberRepositoryInterface
    {
        return resolve(MemberRepositoryInterface::class);
    }

    public function inviteFriends(User $context, int $eventId, array $userIds): void
    {
        $event = $this->eventRepository()->find($eventId);
        policy_authorize(EventPolicy::class, 'invite', $context, $event);

        /** @var UserEntityModel[] $users */
        $users = UserEntity::getByIds($userIds);

        foreach ($users as $user) {
            if ($this->eventMemberRepository()->isJoined($event, $user->detail)) {
                continue;
            }
            $isMember = false;

            if ($event->owner instanceof HasPrivacyMember) {
                $isMember = $event->owner->isMember($user->detail);
            }

            if (!$this->isFriend($context, $user->detail) && !$isMember) {
                continue;
            }

            $data = [
                'event_id'   => $eventId,
                'owner_id'   => $user->entityId(),
                'owner_type' => $user->entityType(),
            ];

            $invite = $this->getInvite($eventId, $user->detail);
            if (null != $invite) {
                if ($invite->status_id == Invite::STATUS_PENDING) {
                    continue;
                }

                $invite->update([
                    'user_id'   => $context->entityId(),
                    'user_type' => $context->entityType(),
                    'status_id' => Invite::STATUS_PENDING,
                ]);

                $response = $invite->toNotification();
                if (is_array($response)) {
                    Notification::send(...$response);
                }

                continue;
            }

            (new Invite(array_merge($data, [
                'user_id'   => $context->entityId(),
                'user_type' => $context->entityType(),
                'status_id' => Invite::STATUS_PENDING,
            ])))->save();
        }
    }

    public function handleLeaveEvent(int $eventId, User $user, bool $notInviteAgain): bool
    {
        $data = [
            'event_id'   => $eventId,
            'owner_id'   => $user->entityId(),
            'owner_type' => $user->entityType(),
        ];

        /** @var Invite $invite */
        $invite = $this->getModel()->newQuery()->where($data)->first();
        if (null != $invite) {
            $status = Invite::STATUS_DECLINED;
            if ($notInviteAgain) {
                $status = Invite::STATUS_NOT_INVITE_AGAIN;
            }

            $invite->update(['status_id' => $status]);
        }

        if ($notInviteAgain && null == $invite) {
            $invite = (new Invite(array_merge($data, [
                'user_id'   => $user->entityId(),
                'user_type' => $user->entityType(),
                'status_id' => Invite::STATUS_NOT_INVITE_AGAIN,
            ])))->save();
        }

        if ($invite instanceof Invite) {
            app('events')->dispatch(
                'notification.delete_notification_by_type_and_item',
                ['event_invite', $invite->entityId(), $invite->entityType()],
                true
            );
        }

        return true;
    }

    public function handleJoinEvent(int $eventId, User $user): void
    {
        $data = [
            'event_id'   => $eventId,
            'owner_id'   => $user->entityId(),
            'owner_type' => $user->entityType(),
        ];

        /** @var Invite $invite */
        $invite = $this->getModel()->newQuery()->where($data)->first();
        if (null != $invite) {
            $invite->update(['status_id' => Invite::STATUS_APPROVED]);
        }
    }

    /**
     * @param User $context
     * @param int  $eventId
     * @param int  $userId
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function deleteEventInvite(User $context, int $eventId, int $userId): bool
    {
        /** @var Invite $invite */
        $invite = $this->getModel()->newQuery()
            ->with(['event'])
            ->where('event_id', $eventId)
            ->where('owner_id', $userId)
            ->firstOrFail();

        policy_authorize(EventPolicy::class, 'removeInvite', $context, $invite);

        return (bool) $invite->delete();
    }

    public function viewInvites(User $context, array $attributes): Paginator
    {
        $eventId = $attributes['event_id'];
        $limit   = $attributes['limit'];
        /** @var Event $event */
        $event = $this->eventRepository()->find($eventId);
        policy_authorize(EventPolicy::class, 'invite', $context, $event);
        $query = $this->getModel()->newQuery();

        if (!$event->isModerator($context)) {
            $query->where('user_id', $context->entityId());
        }

        return $query
            ->with(['userEntity', 'ownerEntity'])
            ->where('event_id', $eventId)
            ->where('status_id', Invite::STATUS_PENDING)
            ->simplePaginate($limit);
    }

    public function getInvite(int $eventId, User $user): ?Invite
    {
        /** @var Invite $invite */
        $invite = $this->getModel()->newQuery()
            ->where([
                'event_id'   => $eventId,
                'owner_id'   => $user->entityId(),
                'owner_type' => $user->entityType(),
            ])->first();

        return $invite;
    }

    /**
     * @param int  $eventId
     * @param User $user
     *
     * @return Invite|null
     */
    public function getPendingInvite(int $eventId, User $user): ?Invite
    {
        /** @var Invite $invite */
        $invite = $this->getModel()->newQuery()
            ->with(['userEntity', 'ownerEntity'])
            ->where([
                'event_id'   => $eventId,
                'owner_id'   => $user->entityId(),
                'owner_type' => $user->entityType(),
                'status_id'  => Invite::STATUS_PENDING,
            ])
            ->first();

        return $invite;
    }

    public function acceptInvite(Event $event, User $user): bool
    {
        $invite = $this->getPendingInvite($event->entityId(), $user);
        if (null == $invite) {
            return false;
        }

        return (bool) $this->eventMemberRepository()->joinEvent($event, $user, Member::ROLE_MEMBER);
    }

    public function declineInvite(Event $event, User $user): bool
    {
        $invite = $this->getPendingInvite($event->entityId(), $user);
        if (null == $invite) {
            return false;
        }

        return $invite->update(['status_id' => Invite::STATUS_DECLINED]);
    }

    /**
     * @inheritDoc
     */
    public function getPendingInvites(Event $event): Collection
    {
        return $this->getModel()->newQuery()
            ->where([
                'event_id'  => $event->entityId(),
                'status_id' => Invite::STATUS_PENDING,
            ])
            ->get();
    }

    public function deleteInvited(int $ownerId): void
    {
        $invites = $this->getModel()->newModelQuery()
            ->where([
                'owner_id' => $ownerId,
            ])
            ->get();

        foreach ($invites as $invite) {
            $invite->delete();

            $this->deleteNotification($invite);
        }
    }

    public function deleteInvite(int $userId): void
    {
        $invites = $this->getModel()->newModelQuery()
            ->where([
                'user_id' => $userId,
            ])
            ->get();

        foreach ($invites as $invite) {
            $invite->delete();

            $this->deleteNotification($invite);
        }
    }

    public function deleteNotification(Invite $invite): void
    {
        $response = $invite->toNotification();

        if (is_array($response)) {
            return;
        }

        app('events')->dispatch('notification.delete_mass_notification_by_item', [$invite], true);
    }
}
