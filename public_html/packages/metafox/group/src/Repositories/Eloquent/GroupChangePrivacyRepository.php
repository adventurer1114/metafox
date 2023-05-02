<?php

namespace MetaFox\Group\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Notification;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Models\GroupChangePrivacy;
use MetaFox\Group\Models\Member;
use MetaFox\Group\Notifications\PendingPrivacyNotification;
use MetaFox\Group\Notifications\SuccessPrivacyNotification;
use MetaFox\Group\Policies\GroupPolicy;
use MetaFox\Group\Repositories\GroupChangePrivacyRepositoryInterface;
use MetaFox\Group\Repositories\GroupRepositoryInterface;
use MetaFox\Group\Repositories\MemberRepositoryInterface;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Repositories\AbstractRepository;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class GroupChangePrivacyRepository.
 *
 * @method GroupChangePrivacy getModel()
 * @method GroupChangePrivacy find($id, $columns = ['*'])()
 */
class GroupChangePrivacyRepository extends AbstractRepository implements GroupChangePrivacyRepositoryInterface
{
    public function model()
    {
        return GroupChangePrivacy::class;
    }

    /**
     * @return GroupRepositoryInterface
     */
    private function groupRepository(): GroupRepositoryInterface
    {
        return resolve(GroupRepositoryInterface::class);
    }

    /**
     * @return MemberRepositoryInterface
     */
    private function groupMemberRepository(): MemberRepositoryInterface
    {
        return resolve(MemberRepositoryInterface::class);
    }

    /**
     * @return PrivacyTypeHandler
     */
    private function getPrivacyTypeHandler(): PrivacyTypeHandler
    {
        return resolve(PrivacyTypeHandler::class);
    }

    /**
     * @inheritDoc
     * @throws ValidatorException
     */
    public function createRequest(Group $group, User $user, array $attributes): bool
    {
        if ($this->isPendingChangePrivacy($group)) {
            return false;
        }
        $numberDays = Settings::get('group.number_days_expiration_change_privacy');

        $data = [
            'expired_at'   => Carbon::now(),
            'group_id'     => $group->entityId(),
            'user_id'      => $user->entityId(),
            'user_type'    => $user->entityType(),
            'is_active'    => GroupChangePrivacy::IS_NOT_ACTIVE,
            'privacy_type' => $attributes['privacy_type'],
            'privacy'      => $this->getPrivacyTypeHandler()->getPrivacy($attributes['privacy_type']),
            'privacy_item' => $this->getPrivacyTypeHandler()->getPrivacyItem($attributes['privacy_type']),
        ];

        /* @var GroupChangePrivacy $groupChangePrivacy */
        if ($numberDays > 0) {
            $data['expired_at'] = Carbon::now()->addDays($numberDays);
            $data['is_active']  = GroupChangePrivacy::IS_ACTIVE;
            $groupChangePrivacy = parent::create($data);
            $this->sentNotificationWhenPending($groupChangePrivacy->entityId());

            return true;
        }

        $groupChangePrivacy = parent::create($data);

        $this->sentNotificationWhenSuccess($groupChangePrivacy->entityId());

        $this->updatePrivacyGroup($group, $attributes['privacy_type']);

        return true;
    }

    /**
     * @param  User                   $user
     * @param  int                    $groupId
     * @return bool
     * @throws AuthorizationException
     */
    public function cancelRequest(User $user, int $groupId): bool
    {
        $now   = Carbon::now();
        $group = $this->groupRepository()->find($groupId);

        policy_authorize(GroupPolicy::class, 'update', $user, $group);

        /** @var $model GroupChangePrivacy */
        $model = $this->getModel()->newQuery()
            ->where([
                'group_id'  => $group->entityId(),
                'is_active' => GroupChangePrivacy::IS_ACTIVE,
            ])
            ->whereDate('expired_at', '>=', $now)->first();

        if (!$model instanceof GroupChangePrivacy) {
            return false;
        }

        $model->update(['is_active' => GroupChangePrivacy::IS_NOT_ACTIVE]);

        app('events')->dispatch(
            'notification.delete_notification_by_type_and_item',
            ['pending_privacy', $model->entityId(), $model->entityType()]
        );

        return true;
    }

    /**
     * @inheritDoc
     */
    public function sentNotificationWhenPending(int $id): void
    {
        $groupChangePrivacy = $this->find($id);
        $group              = $this->groupRepository()->find($groupChangePrivacy->group->entityId());
        $members            = $this->groupMemberRepository()->getModel()
            ->newQuery()->with('userEntity')
            ->where('group_id', $group->entityId())
            ->where('member_type', Member::ADMIN)->get();

        $notification = new PendingPrivacyNotification($groupChangePrivacy);

        foreach ($members as $member) {
            $notificationParams = [$member->user, $notification];
            Notification::send(...$notificationParams);
        }
    }

    public function isPendingChangePrivacy(Group $group): bool
    {
        return $this->getModel()->newQuery()
            ->where([
                'group_id'  => $group->entityId(),
                'is_active' => GroupChangePrivacy::IS_ACTIVE,
            ])->exists();
    }

    /**
     * @inheritDoc
     */
    public function sentNotificationWhenSuccess(int $id): void
    {
        $groupChangePrivacy = $this->find($id);

        $group = $this->groupRepository()->find($groupChangePrivacy->group->entityId());

        $members = $this->groupMemberRepository()->getGroupMembers($group->entityId());

        $notification = new SuccessPrivacyNotification($groupChangePrivacy);

        foreach ($members as $member) {
            $notificationParams = [$member->user, $notification];
            Notification::send(...$notificationParams);
        }
    }

    /**
     * @param  Group  $group
     * @param  string $privacyType
     * @return void
     */
    public function updatePrivacyGroup(Group $group, string $privacyType): void
    {
        $group->update([
            'privacy'      => $this->getPrivacyTypeHandler()->getPrivacy($privacyType),
            'privacy_item' => $this->getPrivacyTypeHandler()->getPrivacyItem($privacyType),
            'privacy_type' => $privacyType,
        ]);
    }
}
