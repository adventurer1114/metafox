<?php

namespace MetaFox\Group\Policies;

use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Models\Group as Resource;
use MetaFox\Group\Repositories\BlockRepositoryInterface;
use MetaFox\Group\Repositories\GroupChangePrivacyRepositoryInterface;
use MetaFox\Group\Support\Facades\Group as GroupFacade;
use MetaFox\Group\Support\InviteType;
use MetaFox\Group\Support\Membership;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;

/**
 * Class GroupPolicy.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @method viewApprove(User $user, ?Content $resource = null)
 * @ignore
 */
class GroupPolicy implements ResourcePolicyInterface
{
    use HasPolicyTrait;
    use CheckModeratorSettingTrait;

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if ($user->hasPermissionTo('group.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('group.view')) {
            return false;
        }

        if ($owner instanceof User) {
            if (!$this->viewOwner($user, $owner)) {
                return false;
            }
        }

        return true;
    }

    public function viewOwner(User $user, User $owner): bool
    {
        // Check can view on owner.
        if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
            return false;
        }

        return true;
    }

    public function view(User $user, Entity $resource, $code = null): bool
    {
        if ($user->hasPermissionTo('group.moderate')) {
            return true;
        }

        $inviteType = null;
        if (!$user->hasPermissionTo('group.view')) {
            return false;
        }

        if (!$resource instanceof Resource) {
            return false;
        }

        $blockRepository = resolve(BlockRepositoryInterface::class);

        if ($blockRepository->isBlocked($resource->entityId(), $user->entityId())) {
            return false;
        }

        if ($resource->privacy_type == PrivacyTypeHandler::SECRET) {
            if ($code != null) {
                $inviteType = InviteType::INVITED_GENERATE_LINK;
            }
            $memberShip = Membership::getMembership($resource, $user, $inviteType);
            if ($memberShip != Membership::NO_JOIN) {
                return true;
            }
        }

        // Check can view on owner.
        if (!PrivacyPolicy::checkPermissionOwner($user, $resource)) {
            return false;
        }

        // Check setting view on resource.
        if (!$this->viewApprove($user, $resource)) {
            return false;
        }

        return $user->hasPermissionTo('group.view');
    }

    public function create(User $user, ?User $owner = null): bool
    {
        return $user->hasPermissionTo('group.create');
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('group.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('group.update')) {
            return false;
        }

        if ($resource instanceof Content) {
            return $this->isGroupAdmin($user, $resource)
                || $this->isGroupOwner($user, $resource);
        }

        return true;
    }

    /**
     * @param  User         $user
     * @param  Content|null $resource
     * @return bool
     */
    public function invite(User $user, ?Content $resource = null): bool
    {
        if ($user->hasPermissionTo('group.moderate')) {
            return true;
        }

        if ($resource instanceof Group) {
            return $resource->isMember($user);
        }

        return true;
    }

    public function viewInvitedOrBlocked(User $user, ?Content $resource = null): bool
    {
        if ($user->hasPermissionTo('group.moderate')) {
            return true;
        }

        if (!$this->viewMembers($user, $resource)) {
            return false;
        }

        return $this->isGroupAdmin($user, $resource)
            || $this->isGroupOwner($user, $resource)
            || $this->isGroupModerator($user, $resource);
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if (!$resource instanceof Resource) {
            return false;
        }

        if ($user->hasPermissionTo('group.moderate')) {
            return true;
        }

        if ($resource instanceof Content) {
            if (!$resource->isApproved() && $user->hasPermissionTo('group.approve')) {
                return true;
            }
        }

        return $this->deleteOwn($user, $resource);
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$user->hasPermissionTo('group.delete')) {
            return false;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param User    $user
     * @param Content $resource
     *
     * @return bool
     */
    public function isGroupOwner(User $user, Content $resource): bool
    {
        if ($user->entityId() != $resource->userId()) {
            return false;
        }

        return true;
    }

    /**
     * @param User    $user
     * @param Content $resource
     *
     * @return bool
     */
    public function isGroupAdmin(User $user, Content $resource): bool
    {
        if (!$resource instanceof HasPrivacyMember) {
            return false;
        }

        if (!$resource->isAdmin($user)) {
            return false;
        }

        return true;
    }

    /**
     * @param User    $user
     * @param Content $resource
     *
     * @return bool
     */
    public function isGroupModerator(User $user, Content $resource): bool
    {
        if (!$resource instanceof HasPrivacyMember) {
            return false;
        }

        if (!$resource->isModerator($user)) {
            return false;
        }

        return true;
    }

    /**
     * @param resource $group
     *
     * @return bool
     */
    public function answerQuestionBeforeJoining(Resource $group): bool
    {
        return Membership::mustAnswerQuestionsBeforeJoining($group);
    }

    public function addMembershipQuestion(User $context, Group $group): bool
    {
        $maxQuestion = GroupFacade::getMaximumMembershipQuestion();

        if ($maxQuestion <= 0) {
            return false;
        }

        $currentQuestions = $group->groupQuestions;

        if (null === $currentQuestions) {
            return true;
        }

        return count($currentQuestions) < $maxQuestion;
    }

    public function managePendingRequestTab(User $context, Group $group): bool
    {
        if ($group->isPublicPrivacy()) {
            return false;
        }

        if ($this->moderate($context, $group)) {
            return true;
        }

        return $this->checkModeratorSetting($context, $group, 'approve_or_deny_membership_request');
    }

    public function manageGroup(User $user, Content $resource): bool
    {
        if (!$resource instanceof Resource) {
            return false;
        }
        if ($user->hasPermissionTo('group.moderate')) {
            return true;
        }

        return $this->isGroupAdmin($user, $resource) || $this->isGroupOwner($user, $resource);
    }

    public function viewMembers(User $user, Content $resource): bool
    {
        return policy_check(MemberPolicy::class, 'viewAny', $user, $resource);
    }

    public function isPendingChangePrivacy(User $user, Content $resource): bool
    {
        if (!$this->isGroupAdmin($user, $resource) || !$this->isGroupOwner($user, $resource)) {
            return false;
        }
        $repository = resolve(GroupChangePrivacyRepositoryInterface::class);

        return $repository->isPendingChangePrivacy($resource);
    }

    public function viewFeedContent(User $user, Group $group, string $status, bool $isYour = false): bool
    {
        if ($status == MetaFoxConstant::ITEM_STATUS_APPROVED) {
            return true;
        }

        if ($user->hasPermissionTo('group.moderate')) {
            return true;
        }

        /*
         * This is past as page feature
         */
        if ($user->entityId() == $group->entityId() && $user->entityType() == $group->entityType()) {
            return true;
        }

        if (!$group->isMember($user)) {
            return false;
        }

        switch ($status) {
            case MetaFoxConstant::ITEM_STATUS_PENDING:
                $isAdmin = $this->checkModeratorSetting($user, $group, 'approve_or_deny_post');
                break;
            default:
                $isAdmin = $this->update($user, $group);
                break;
        }

        if (!$isAdmin) {
            return $isYour;
        }

        if ($isYour && !in_array(
            $status,
            [MetaFoxConstant::ITEM_STATUS_APPROVED, MetaFoxConstant::ITEM_STATUS_REMOVED]
        )) {
            return false;
        }

        return true;
    }

    public function viewReportContent(User $user, Group $group): bool
    {
        if ($user->hasPermissionTo('group.moderate')) {
            return true;
        }

        if (!$group->isMember($user)) {
            return false;
        }

        $isModerator = $this->checkModeratorSetting($user, $group, 'remove_post_and_comment_on_post');

        return $isModerator && $this->update($user);
    }

    public function approve(User $user, ?Group $group): bool
    {
        if (!$group instanceof Group) {
            return false;
        }

        if ($user->hasPermissionTo('group.approve')) {
            return true;
        }

        return false;
    }

    public function join(User $user, ?Group $group): bool
    {
        if (!$group instanceof Group) {
            return false;
        }

        if ($user->hasPermissionTo('group.moderate')) {
            return true;
        }

        $membership = Membership::getMembership($group, $user);
        if ($membership == Membership::INVITED) {
            return true;
        }

        if ($group->isSecretPrivacy()) {
            return false;
        }

        return true;
    }

    public function markAnnouncement(User $user, ?Group $group): bool
    {
        return $this->manageGroup($user, $group);
    }

    public function moderate(User $user, Group $group): bool
    {
        if ($user->hasPermissionTo('group.moderate')) {
            return true;
        }

        return $this->isGroupOwner($user, $group);
    }

    public function addNewAdmin(User $user, Group $group): bool
    {
        if (!$group->isApproved()) {
            return false;
        }

        return $this->manageGroup($user, $group);
    }

    public function addNewModerator(User $user, Group $group): bool
    {
        if (!$group->isApproved()) {
            return false;
        }

        return $this->manageGroup($user, $group);
    }

    public function uploadCover(User $user, Group $group): bool
    {
        if (!$user->hasPermissionTo('photo.create')) {
            return false;
        }

        return $user->hasPermissionTo('group.upload_cover') && $this->manageGroup($user, $group);
    }

    public function editCover(User $user, Group $group): bool
    {
        if (!$this->update($user, $group)) {
            return false;
        }

        return $group->cover_id > 0;
    }
}
