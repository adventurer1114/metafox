<?php

namespace MetaFox\Quiz\Policies;

use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class QuizPolicy.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class QuizPolicy implements ResourcePolicyInterface
{
    use HasPolicyTrait;
    use CheckModeratorSettingTrait;

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if ($user->hasPermissionTo('quiz.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('quiz.view')) {
            return false;
        }

        if ($owner instanceof User) {
            if (!$this->viewOwner($user, $owner)) {
                return false;
            }
        }

        return true;
    }

    public function viewOwner(User $user, ?User $owner = null): bool
    {
        if ($owner == null) {
            return false;
        }

        // Check can view on owner.
        if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
            return false;
        }

        if (!UserPrivacy::hasAccess($user, $owner, 'quiz.view_browse_quizzes')) {
            return false;
        }

        return true;
    }

    public function view(User $user, Entity $resource): bool
    {
        $isApproved = $resource->isApproved();

        if (!$isApproved && $user->isGuest()) {
            return false;
        }

        if ($user->hasPermissionTo('quiz.moderate')) {
            return true;
        }

        // check user role permission
        if (!$user->hasPermissionTo('quiz.view')) {
            return false;
        }

        $owner = $resource->owner;

        if (!$owner instanceof User) {
            return false;
        }

        if (!$this->viewOwner($user, $owner)) {
            return false;
        }

        // Check can view on resource.
        if (PrivacyPolicy::checkPermission($user, $resource) == false) {
            return false;
        }

        if ($isApproved) {
            return true;
        }

        if ($owner instanceof HasPrivacyMember) {
            if ($this->checkModeratorSetting($user, $owner, 'approve_or_deny_post')) {
                return true;
            }
        }

        if ($resource->userId() == $user->entityId()) {
            return true;
        }

        return $user->hasPermissionTo('quiz.approve');
    }

    public function create(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('quiz.create')) {
            return false;
        }

        if ($owner instanceof User) {
            if ($owner->entityId() != $user->entityId()) {
                if ($owner->entityType() == 'user') {
                    return false;
                }

                if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
                    return false;
                }

                if (!UserPrivacy::hasAccess($user, $owner, 'quiz.share_quizzes')) {
                    return false;
                }
            }
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('quiz.moderate')) {
            return true;
        }

        return $this->updateOwn($user, $resource);
    }

    public function updateOwn(User $user, ?Content $resource = null): bool
    {
        if (!$user->hasPermissionTo('quiz.update')) {
            return false;
        }

        if (null === $resource) {
            return true;
        }

        if ($user->entityId() != $resource->userId()) {
            return false;
        }

        return true;
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('quiz.moderate')) {
            return true;
        }

        if ($resource instanceof Content) {
            if (!$resource->isApproved() && $user->hasPermissionTo('quiz.approve')) {
                return true;
            }
        }

        return $this->deleteOwn($user, $resource);
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$user->hasPermissionTo('quiz.delete')) {
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
     * @param User         $user
     * @param Content|null $resource
     *
     * @return bool
     */
    public function play(User $user, ?Content $resource = null): bool
    {
        if (!$resource) {
            return false;
        }

        if ($resource instanceof Content) {
            if (!$this->view($user, $resource)) {
                return false;
            }
        }

        if ($resource instanceof HasApprove) {
            if (!$resource->isApproved()) {
                return false;
            }
        }

        if (!$user->hasPermissionTo('quiz.play')) {
            return false;
        }

        return true;
    }

    public function viewMemberResult(User $user, Entity $resource): bool
    {
        if ($user->hasPermissionTo('quiz.moderate')) {
            return true;
        }

        // check user role permission
        if (!$user->hasPermissionTo('quiz.view')) {
            return false;
        }

        $owner = $resource->owner;

        if (!$owner instanceof User) {
            return false;
        }

        if (!$this->viewOwner($user, $owner)) {
            return false;
        }

        // Check can view on resource.
        if (PrivacyPolicy::checkPermission($user, $resource) == false) {
            return false;
        }

        if ($resource->userId() == $user->entityId()) {
            return true;
        }

        return true;
    }
}
