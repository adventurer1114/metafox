<?php

namespace MetaFox\Group\Repositories;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Query\Builder;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Models\GroupInviteCode;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Repository\Contracts\HasFeature;
use MetaFox\Platform\Support\Repository\Contracts\HasSponsor;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Interface Group.
 * @mixin BaseRepository
 * @method Group getModel()
 * @method Group find($id, $columns = ['*'])()
 */
interface GroupRepositoryInterface extends HasSponsor, HasFeature
{
    /**
     * @param User                 $context
     * @param User                 $owner
     * @param array<string, mixed> $attributes
     *
     * @return Paginator
     * @throws AuthorizationException
     */
    public function viewGroups(User $context, User $owner, array $attributes): Paginator;

    /**
     * @param  int   $id
     * @return Group
     */
    public function getGroup(int $id): Group;

    /**
     * @param  User                   $context
     * @param  int                    $id
     * @param  GroupInviteCode|null   $inviteCode
     * @return Group
     * @throws AuthorizationException
     */
    public function viewGroup(User $context, int $id, ?GroupInviteCode $inviteCode): Group;

    /**
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @return Group
     * @throws AuthorizationException
     * @throws ValidatorException
     */
    public function createGroup(User $context, User $owner, array $attributes): Group;

    /**
     * @param User                 $context
     * @param int                  $id
     * @param array<string, mixed> $attributes
     *
     * @return Group
     * @throws AuthorizationException
     */
    public function updateGroup(User $context, int $id, array $attributes): Group;

    /**
     * @param User $context
     * @param int  $id
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function deleteGroup(User $context, int $id): bool;

    /**
     * @param User   $context
     * @param int    $id
     * @param string $imageBase46
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function updateAvatar(User $context, int $id, string $imageBase46): bool;

    /**
     * @param User                 $context
     * @param int                  $id
     * @param array<string, mixed> $attributes
     *
     * @return array<string,          mixed>
     * @throws AuthorizationException
     */
    public function updateCover(User $context, int $id, array $attributes): array;

    /**
     * @param int $limit
     *
     * @return Paginator
     */
    public function findFeature(int $limit = 4): Paginator;

    /**
     * @param int $limit
     *
     * @return Paginator
     */
    public function findSponsor(int $limit = 4): Paginator;

    /**
     * @param User $context
     * @param int  $id
     *
     * @return Content
     * @throws AuthorizationException
     */
    public function approve(User $context, int $id): Content;

    /**
     * @param Content $model
     *
     * @return bool
     */
    public function isPending(Content $model): bool;

    /**
     * @param User $context
     * @param int  $id
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function removeCover(User $context, int $id): bool;

    /**
     * @param User                 $context
     * @param array<string, mixed> $params
     * @param bool                 $getEnoughLimit
     *
     * @return array<mixed>
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getSuggestion(User $context, array $params = [], bool $getEnoughLimit = true): array;

    /**
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @return Paginator
     */
    public function getGroupForMention(User $context, array $attributes): Paginator;

    /**
     * @param User $context
     * @param int  $id
     * @param int  $pendingMode
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function updatePendingMode(User $context, int $id, int $pendingMode): bool;

    /**
     * @param Group $group
     *
     * @return bool
     */
    public function hasGroupRule(Group $group): bool;

    /**
     * @param  Group $group
     * @return bool
     */
    public function hasGroupRuleConfirmation(Group $group): bool;

    /**
     * @param  Group $group
     * @return bool
     */
    public function hasGroupQuestionsConfirmation(Group $group): bool;

    /**
     * @param Group $group
     *
     * @return bool
     */
    public function hasGroupQuestions(Group $group): bool;

    /**
     * @param Group $group
     *
     * @return bool
     */
    public function hasMembershipQuestion(Group $group): bool;

    /**
     * @param  User $context
     * @param  int  $id
     * @param  bool $isConfirmation
     * @return bool
     */
    public function updateRuleConfirmation(User $context, int $id, bool $isConfirmation): Group;

    /**
     * @param  User  $context
     * @param  int   $id
     * @param  bool  $isConfirmation
     * @return Group
     */
    public function updateAnswerMembershipQuestion(User $context, int $id, bool $isConfirmation): Group;

    /**
     * @param  User    $user
     * @return Builder
     */
    public function getGroupBuilder(User $user): Builder;

    /**
     * @param  Group $group
     * @param  User  $context
     * @return array
     */
    public function toPendingNotifiables(Group $group, User $context): array;

    /**
     * @param  User    $context
     * @param  Content $resource
     * @param  Group   $group
     * @return bool
     */
    public function hasDeleteFeedPermission(User $context, Content $resource, Group $group): bool;

    /**
     * @param  int  $groupId
     * @return void
     */
    public function handleSendInviteNotification(int $groupId): void;
}
