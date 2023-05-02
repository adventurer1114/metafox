<?php

namespace MetaFox\Group\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use MetaFox\Core\Traits\CollectTotalItemStatTrait;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Models\GroupInviteCode;
use MetaFox\Group\Models\Invite;
use MetaFox\Group\Policies\CategoryPolicy;
use MetaFox\Group\Policies\GroupPolicy;
use MetaFox\Group\Repositories\CategoryRepositoryInterface;
use MetaFox\Group\Repositories\GroupChangePrivacyRepositoryInterface;
use MetaFox\Group\Repositories\GroupRepositoryInterface;
use MetaFox\Group\Repositories\InviteRepositoryInterface;
use MetaFox\Group\Support\Browse\Scopes\Group\BlockedScope;
use MetaFox\Group\Support\Browse\Scopes\Group\PrivacyScope;
use MetaFox\Group\Support\Browse\Scopes\Group\SortScope;
use MetaFox\Group\Support\Browse\Scopes\Group\ViewScope;
use MetaFox\Group\Support\CacheManager;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasApprove as HasApproveContract;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasSponsor as HasSponsorContract;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\CategoryScope;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\Platform\Support\Browse\Scopes\WhenScope;
use MetaFox\Platform\Support\Repository\HasApprove;
use MetaFox\Platform\Support\Repository\HasFeatured;
use MetaFox\Platform\Support\Repository\HasSponsor;
use MetaFox\User\Support\Facades\UserValue;

/**
 * Class GroupRepository.
 * @method Group getModel()
 * @method Group find($id, $columns = ['*'])()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @inore
 */
class GroupRepository extends AbstractRepository implements GroupRepositoryInterface
{
    use HasSponsor;
    use HasFeatured;
    use HasApprove;
    use CollectTotalItemStatTrait;

    public function model(): string
    {
        return Group::class;
    }

    /**
     * @return PrivacyTypeHandler
     */
    private function getPrivacyTypeHandler(): PrivacyTypeHandler
    {
        return resolve(PrivacyTypeHandler::class);
    }

    /**
     * @return InviteRepositoryInterface
     */
    private function groupInviteRepository(): InviteRepositoryInterface
    {
        return resolve(InviteRepositoryInterface::class);
    }

    /**
     * @return GroupChangePrivacyRepositoryInterface
     */
    private function changePrivacyRepository(): GroupChangePrivacyRepositoryInterface
    {
        return resolve(GroupChangePrivacyRepositoryInterface::class);
    }

    /**
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function viewGroups(User $context, User $owner, array $attributes): Paginator
    {
        policy_authorize(GroupPolicy::class, 'viewAny', $context, $owner);

        $sort       = $attributes['sort'];
        $sortType   = $attributes['sort_type'];
        $when       = $attributes['when'];
        $view       = $attributes['view'];
        $search     = $attributes['q'];
        $limit      = $attributes['limit'];
        $categoryId = $attributes['category_id'];
        $profileId  = $attributes['user_id'];

        switch ($view) {
            case Browse::VIEW_FEATURE:
                return $this->findFeature($limit);
            case Browse::VIEW_SPONSOR:
                return $this->findSponsor($limit);
            case Browse::VIEW_PENDING:
                if ($profileId != 0 && $profileId == $context->entityId()) {
                    break;
                }

                if ($context->isGuest() || !$context->hasPermissionTo('group.approve')) {
                    throw new AuthorizationException(__p('core::validation.this_action_is_unauthorized'), 403);
                }

                break;
        }

        if ($context->entityId() && $profileId == $context->entityId() && $view != Browse::VIEW_PENDING) {
            $attributes['view'] = $view = Browse::VIEW_MY;
        }
        $categoryId = Arr::get($attributes, 'category_id', 0);

        if ($categoryId > 0) {
            $category = resolve(CategoryRepositoryInterface::class)->find($categoryId);

            policy_authorize(CategoryPolicy::class, 'viewActive', $context, $category);
        }

        $sortScope = new SortScope();
        $sortScope->setSort($sort)->setSortType($sortType);

        $whenScope = new WhenScope();
        $whenScope->setWhen($when);

        $viewScope = new ViewScope();
        $viewScope->setUserContext($context)->setView($view)->setProfileId($profileId);

        $blockedScope = new BlockedScope();
        $blockedScope->setContextId($context->entityId());

        $query = $this->getModel()->newQuery()
            ->with(['userEntity'])
            ->withCount(['pendingRequests as pending_requests_count']);

        if ($search != '') {
            $query = $query->addScope(new SearchScope($search, ['name']));
        }

        if (!$context->hasPermissionTo('group.moderate')) {
            // Scopes.
            $privacyScope = new PrivacyScope();
            $privacyScope
                ->setUserId($context->entityId())
                ->setView($view);

            $query = $query->addScope($privacyScope);
        }

        if ($categoryId > 0) {
            if (!is_array($categoryId)) {
                $categoryId = [$categoryId];
            }

            $categoryScope = new CategoryScope();
            $categoryScope->setCategories($categoryId);
            $query->addScope($categoryScope);
        }

        if ($owner->entityId() != $context->entityId()) {
            $query->where('groups.user_id', '=', $owner->entityId())
                ->where('groups.is_approved', HasApproveContract::IS_APPROVED);

            $viewScope->setIsViewProfile(true);
        }

        if (isset($attributes['privacy_type'])) {
            $query->where('groups.privacy_type', '=', $attributes['privacy_type']);
        }

        if (isset($attributes['not_in_ids']) && !empty($attributes['not_in_ids'])) {
            $query->whereNotIn('groups.id', $attributes['not_in_ids']);
        }

        $groupData = $query->addScope($sortScope)
            ->addScope($whenScope)
            ->addScope($viewScope)
            ->addScope($blockedScope)
            ->simplePaginate($limit, ['groups.*']);

        $attributes['current_page'] = $groupData->currentPage();
        //Load sponsor on first page only
        if (!$this->hasSponsorView($attributes) || $sort == Browse::SORT_LATEST) {
            return $groupData;
        }

        $userId    = $context->entityId();
        $cacheKey  = sprintf(CacheManager::SPONSOR_ITEM_CACHE, $userId);
        $cacheTime = CacheManager::SPONSOR_ITEM_CACHE_ITEM;

        return $this->transformPaginatorWithSponsor($groupData, $cacheKey, $cacheTime);
    }

    public function getGroup(int $id): Group
    {
        return $this->with(['user', 'category', 'groupText'])
            ->withCount(['pendingRequests as pending_requests_count'])
            ->find($id);
    }

    public function viewGroup(User $context, int $id, ?GroupInviteCode $inviteCode): Group
    {
        $group = $this->getGroup($id);
        $code  = null;
        if (!empty($inviteCode)) {
            $code = $inviteCode->code;
        }

        policy_authorize(GroupPolicy::class, 'view', $context, $group, $code);

        return $group;
    }

    public function createGroup(User $context, User $owner, array $attributes): Group
    {
        policy_authorize(GroupPolicy::class, 'create', $context);

        $attributes = array_merge($attributes, [
            'user_id'              => $context->entityId(),
            'user_type'            => $context->entityType(),
            'privacy'              => $this->getPrivacyTypeHandler()->getPrivacy($attributes['privacy_type']),
            'privacy_item'         => $this->getPrivacyTypeHandler()->getPrivacyItem($attributes['privacy_type']),
            'is_rule_confirmation' => false,
        ]);

        //only apply auto approve when $context == $owner
        if ($context->entityId() == $owner->entityId()) {
            if (!$context->hasPermissionTo('group.auto_approved')) {
                $attributes['is_approved'] = 0;
            }
        }

        /** @var Group $group */
        $group = parent::create($attributes);

        if (!empty($attributes['user_ids'])) {
            $this->groupInviteRepository()->inviteFriends($context, $group->entityId(), $attributes['user_ids']);
        }

        $group->refresh();

        return $group;
    }

    public function updateGroup(User $context, int $id, array $attributes): Group
    {
        $group = $this->find($id);

        policy_authorize(GroupPolicy::class, 'update', $context, $group);

        if (Arr::has($attributes, 'privacy_type') && $attributes['privacy_type'] != $group->getPrivacyType()) {
            if ($group->isClosedPrivacy() && PrivacyTypeHandler::PUBLIC == $attributes['privacy_type'] || $group->isSecretPrivacy()) {
                abort(403, __p('group::phrase.change_privacy_group_error'));
            }

            $result = $this->changePrivacyRepository()->createRequest($group, $context, $attributes);
            if (!$result) {
                abort(403, __p('group::phrase.request_change_privacy_group_exists'));
            }
            unset($attributes['privacy_type']);
        }

        $group->update($attributes);

        $group->refresh();

        return $group;
    }

    public function deleteGroup(User $context, int $id): bool
    {
        $group = $this->find($id);

        policy_authorize(GroupPolicy::class, 'delete', $context, $group);

        /*
         * Please move this dispatch to forceDelete when implementing soft delete if need
         */
        app('events')->dispatch('user.deleting', [$group]);

        $group->delete();

        /*
         * Please move this dispatch to forceDelete when implementing soft delete if need
         */
        app('events')->dispatch('user.deleted', [$group]);

        return true;
    }

    public function updateAvatar(User $context, int $id, string $imageBase46): bool
    {
        $group = $this->find($id);

        policy_authorize(GroupPolicy::class, 'update', $context, $group);

        $image = upload()->convertBase64ToUploadedFile($imageBase46);

        $params = [
            'privacy' => $group->privacy,
            'path'    => 'group',
            'files'   => [
                [
                    'file' => $image,
                ],
            ],
        ];

        /** @var Collection $photos */
        $photos = app('events')->dispatch('photo.create', [$context, $group, $params, 1], true);

        $photos = $photos->toArray();
        $group->update([
            'avatar_id'      => $photos[0]['id'],
            'avatar_type'    => 'photo',
            'avatar_file_id' => $photos[0]['image_file_id'],
        ]);

        return true;
    }

    public function updateCover(User $context, int $id, array $attributes): array
    {
        $group = $this->find($id);

        policy_authorize(GroupPolicy::class, 'update', $context, $group);

        $coverData = [];

        $positionData = [];

        $feedId = 0;

        if (isset($attributes['position'])) {
            $positionData['cover_photo_position'] = $attributes['position'];
        }

        if (isset($attributes['image'])) {
            policy_authorize(GroupPolicy::class, 'uploadCover', $context, $group);

            $params = [
                'privacy'         => $group->privacy,
                'path'            => 'group',
                'thumbnail_sizes' => $group->getCoverSizes(),
                'files'           => [
                    [
                        'file' => $attributes['image'],
                    ],
                ],
            ];

            /** @var Collection $photos */
            $photos = app('events')->dispatch(
                'photo.create',
                [$context, $group, $params, 2, Group::GROUP_UPDATE_COVER_ENTITY_TYPE],
                true
            );

            if (empty($photos)) {
                abort(400, __('validation.something_went_wrong_please_try_again'));
            }

            foreach ($photos as $photo) {
                $photo->toArray();

                $coverData = [
                    'cover_id'             => $photo['id'],
                    'cover_type'           => 'photo',
                    'cover_file_id'        => $photo['image_file_id'],
                    'cover_photo_position' => null,
                ];

                break;
            }
            unset($attributes['image']);
        }

        $group->update(array_merge($attributes, $coverData, $positionData));

        $group->refresh()->with('user');

        // $group->cover;//get photo -> feed

        return [
            'user'           => $group,
            'feed_id'        => $feedId,
            'cover_resource' => ResourceGate::asItem($group->cover()->first(), false),
            'is_pending'     => false, //Todo check setting
        ];
    }

    public function removeCover(User $context, int $id): bool
    {
        $group = $this->find($id);

        policy_authorize(GroupPolicy::class, 'editCover', $context, $group);

        return $group->update($group->getCoverDataEmpty());
    }

    public function findFeature(int $limit = 4): Paginator
    {
        return $this->getModel()->newQuery()
            ->where('is_featured', HasFeature::IS_FEATURED)
            ->where('is_approved', HasApproveContract::IS_APPROVED)
            ->orderByDesc(HasFeature::FEATURED_AT_COLUMN)
            ->simplePaginate($limit);
    }

    public function findSponsor(int $limit = 4): Paginator
    {
        return $this->getModel()->newQuery()
            ->where('is_sponsor', HasSponsorContract::IS_SPONSOR)
            ->where('is_approved', HasApproveContract::IS_APPROVED)
            ->simplePaginate($limit);
    }

    /**
     * @inerhitDoc
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getSuggestion(User $context, array $params = [], bool $getEnoughLimit = true): array
    {
        if (!app_active('metafox/friend')) {
            return [];
        }

        $query = DB::table('friends as f')
            ->select('groups.*')
            ->join('core_privacy_members as member', function (JoinClause $join) use ($context) {
                $join->on('member.user_id', '=', 'f.owner_id');
                $join->where('member.user_id', '!=', $context->entityId());
            })
            ->rightJoin('core_privacy as privacy', function (JoinClause $join) {
                $join->on('privacy.privacy_id', '=', 'member.privacy_id');

                $join->where('privacy.item_type', '=', Group::ENTITY_TYPE);
                $join->where('privacy.privacy', '=', MetaFoxPrivacy::FRIENDS);
                $join->where('privacy.privacy_type', '=', Group::GROUP_MEMBERS);
            })
            ->rightJoin('groups', function (JoinClause $join) {
                $join->on('groups.id', '=', 'privacy.item_id');
                $join->where('groups.privacy_type', '=', PrivacyTypeHandler::PUBLIC);
            })
            ->leftJoin('core_privacy_members as our_member', function (JoinClause $join) use ($context) {
                $join->on('our_member.privacy_id', '=', 'privacy.privacy_id');
                $join->where('our_member.user_id', '=', $context->entityId());
            })
            ->where('f.user_id', '=', $context->entityId())
            ->whereNull('our_member.user_id');

        $limit = 3;

        if (isset($params['limit'])) {
            $limit = $params['limit'];
        }

        $query->limit($limit);

        $query->orderBy('groups.id', 'DESC');

        $data = $query->get();

        $suggestGroups = [];

        if ($data->count() > 0) {
            $suggestGroups = $this->convertToGroup($data);
        }

        if ($data->count() >= $limit) {
            return $suggestGroups;
        }

        if ($getEnoughLimit === false) {
            return $suggestGroups;
        }

        $suggestGroupIds = array_keys($suggestGroups);

        $moreGroupParams = [
            'limit'        => $limit - $data->count(),
            'privacy_type' => PrivacyTypeHandler::PUBLIC,
            'not_in_ids'   => $suggestGroupIds,
            'view'         => ViewScope::VIEW_DEFAULT,
            'sort'         => SortScope::SORT_DEFAULT,
            'sort_type'    => SortScope::SORT_TYPE_DEFAULT,
            'when'         => WhenScope::WHEN_DEFAULT,
            'type_id'      => 0,
            'category_id'  => $params['category_id'] ?? 0,
            'q'            => '',
            'user_id'      => 0,
        ];

        $moreGroups = $this->viewGroups($context, $context, $moreGroupParams)->items();

        foreach ($moreGroups as $moreGroup) {
            $suggestGroups[] = $moreGroup;
        }

        return $suggestGroups;
    }

    /**
     * @param Collection $collection
     *
     * @return array<mixed>
     */
    private function convertToGroup(Collection $collection): array
    {
        $result = [];

        foreach ($collection as $item) {
            $json = json_encode($item);
            if ($json == false) {
                continue;
            }
            $data = json_decode($json, true);

            $group = new Group();
            $group->forceFill($data);
            $result[] = $group;
        }

        return $result;
    }

    public function getGroupForMention(User $context, array $attributes): Paginator
    {
        $search = $attributes['q'];
        $limit  = $attributes['limit'];

        $query = $this->getModel()->newQuery()
            ->join('group_members AS gm', function (JoinClause $join) use ($context) {
                $join->on('gm.group_id', '=', 'groups.id')
                    ->where('gm.user_id', $context->entityId())
                    ->where('groups.is_approved', HasApproveContract::IS_APPROVED)
                    ->where('groups.privacy_type', PrivacyTypeHandler::PUBLIC);
            });

        if ('' != $search) {
            $query->orWhere('groups.name', $this->likeOperator(), $search . '%');
        }

        return $query->simplePaginate($limit, ['groups.*']);
    }

    public function updatePendingMode(User $context, int $id, int $pendingMode): bool
    {
        $group = $this->find($id);
        policy_authorize(GroupPolicy::class, 'manageGroup', $context, $group);

        return $group->update(['pending_mode' => $pendingMode]);
    }

    public function hasGroupRule(Group $group): bool
    {
        return $group->groupRules()->exists();
    }

    public function hasGroupRuleConfirmation(Group $group): bool
    {
        return $this->hasGroupRule($group) && $group->is_rule_confirmation;
    }

    public function hasGroupQuestionsConfirmation(Group $group): bool
    {
        return $this->hasGroupQuestions($group) && $group->is_answer_membership_question;
    }

    public function hasGroupQuestions(Group $group): bool
    {
        return $group->groupQuestions()->exists();
    }

    public function hasMembershipQuestion(Group $group): bool
    {
        if ($this->hasGroupQuestions($group)) {
            return true;
        }

        return $this->hasGroupRule($group);
    }

    public function updateRuleConfirmation(User $context, int $id, bool $isConfirmation): Group
    {
        $group = $this->find($id);

        policy_authorize(GroupPolicy::class, 'manageGroup', $context, $group);

        return $this->update(['is_rule_confirmation' => $isConfirmation], $id);
    }

    public function updateAnswerMembershipQuestion(User $context, int $id, bool $isConfirmation): Group
    {
        $group = $this->find($id);

        policy_authorize(GroupPolicy::class, 'update', $context, $group);

        return $this->update(['is_answer_membership_question' => $isConfirmation], $id);
    }

    public function getGroupBuilder(User $user): Builder
    {
        return DB::table('user_entities')
            ->select('user_entities.id')
            ->join('groups', function (JoinClause $joinClause) {
                $joinClause->on('groups.id', '=', 'user_entities.id')
                    ->where('groups.is_approved', '=', 1);
            })
            ->leftJoin('user_blocked as blocked_owner', function (JoinClause $join) use ($user) {
                $join->on('blocked_owner.owner_id', '=', 'user_entities.id')
                    ->where('blocked_owner.user_id', '=', $user->entityId());
            })
            ->leftJoin('user_blocked as blocked_user', function (JoinClause $join) use ($user) {
                $join->on('blocked_user.user_id', '=', 'user_entities.id')
                    ->where('blocked_user.owner_id', '=', $user->entityId());
            })
            ->leftJoin('group_members', function (JoinClause $joinClause) use ($user) {
                $joinClause->on('group_members.group_id', '=', 'groups.id')
                    ->where('group_members.user_id', '=', $user->entityId());
            })
            ->where(function (Builder $builder) {
                $builder->where('groups.privacy_type', '=', PrivacyTypeHandler::PUBLIC)
                    ->orWhere(function (Builder $builder) {
                        $builder->where('groups.privacy_type', '=', PrivacyTypeHandler::CLOSED)
                            ->whereNotNull('group_members.id');
                    });
            })
            ->whereNull('blocked_owner.owner_id')
            ->whereNull('blocked_user.user_id');
    }

    public function toPendingNotifiables(Group $group, User $context): array
    {
        $admins = $group->admins()
            ->with(['user'])
            ->get()
            ->map(function ($admin) {
                return $admin->user;
            });

        $notifiables = collect($admins);

        if (UserValue::getUserValueSettingByName($group, 'approve_or_deny_post')) {
            $moderators = $group->moderators()
                ->with(['user'])
                ->get()
                ->map(function ($moderator) {
                    return $moderator->user;
                });

            foreach ($moderators as $moderator) {
                $notifiables->push($moderator);
            }
        }

        return $notifiables
            ->unique('id')
            ->filter(function ($notifiable) use ($context) {
                return $notifiable->entityId() != $context->entityId();
            })
            ->all();
    }

    public function hasDeleteFeedPermission(User $context, Content $resource, Group $group): bool
    {
        return $context->entityId() == $resource->userId();
    }

    /**
     * @inheritDoc
     */
    public function handleSendInviteNotification(int $groupId): void
    {
        $group   = $this->find($groupId);
        $invites = $group->invites;
        foreach ($invites as $invite) {
            if ($invite instanceof Invite) {
                Notification::send(...$invite->toNotification());
            }
        }
    }
}
