<?php

namespace MetaFox\Forum\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use MetaFox\Forum\Models\Forum;
use MetaFox\Forum\Models\ForumThread;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class ForumThreadPolicy.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ForumThreadPolicy implements ResourcePolicyInterface
{
    use HandlesAuthorization;
    use HasPolicyTrait;

    protected string $type = ForumThread::ENTITY_TYPE;

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if ($user->hasPermissionTo('forum_thread.moderate')) {
            return true;
        }

        if (!$this->viewForum($user)) {
            return false;
        }

        if ($owner instanceof User) {
            if (!$this->viewOwner($user, $owner)) {
                return false;
            }
        }

        return true;
    }

    private function viewForum(User $user)
    {
        return policy_check(ForumPolicy::class, 'viewAny', $user);
    }

    public function viewOwner(User $user, ?User $owner = null): bool
    {
        if ($owner == null) {
            return false;
        }

        // Check can view on owner.
        if (PrivacyPolicy::checkPermissionOwner($user, $owner) === false) {
            return false;
        }

        if ($user->hasPermissionTo('forum_thread.moderate')) {
            return true;
        }

        if (!$this->viewForum($user)) {
            return false;
        }

        return true;
    }

    public function view(User $user, Entity $resource): bool
    {
        $owner = $resource->owner;

        if (!$owner instanceof User) {
            return false;
        }

        $isApproved = $resource->isApproved();

        if (!$isApproved && $user->isGuest()) {
            return false;
        }

        if ($user->hasPermissionTo($this->type . '.moderate')) {
            return true;
        }

        if (!$this->viewForum($user)) {
            return false;
        }

        if (!$this->viewOwner($user, $owner)) {
            return false;
        }

        // Check can view on resource.
        if (PrivacyPolicy::checkPermission($user, $resource) == false) {
            return false;
        }

        if (!$isApproved) {
            if ($user->hasPermissionTo('forum_thread.approve')) {
                return true;
            }

            if ($user->entityId() == $resource->userId()) {
                return true;
            }

            return false;
        }

        return true;
    }

    public function updateLastRead(User $context, Content $resource): bool
    {
        if (!$this->view($context, $resource)) {
            return false;
        }

        return $context->entityId() > 0;
    }

    public function create(User $user, ?User $owner = null): bool
    {
        if ($user->hasPermissionTo('forum_thread.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo($this->type . '.create')) {
            return false;
        }

        if ($owner instanceof User) {
            // Check can view on owner.
            if ($owner->entityId() != $user->entityId()) {
                if ($owner->entityType() == 'user') {
                    return false;
                }
                if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
                    return false;
                }

                if (!UserPrivacy::hasAccess($user, $owner, 'forum.share_forum_thread')) {
                    return false;
                }
            }
        }

        return true;
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo($this->type . '.moderate')) {
            return true;
        }

        if (!$this->viewForum($user)) {
            return false;
        }

        if ($resource instanceof Content) {
            if (!$resource->isApproved() && $user->hasPermissionTo($this->type . '.approve')) {
                return true;
            }
        }

        return $this->deleteOwn($user, $resource);
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo($this->type . '.delete_own')) {
            if ($user->entityId() == $resource->userId()) {
                return true;
            }
        }

        return false;
    }

    public function subscribe(User $user, Content $resource): bool
    {
        if (!$this->checkResourcePermission('subscribe', $user, $resource, false, false)) {
            return false;
        }

        if (!$resource->isApproved()) {
            return false;
        }

        return true;
    }

    public function checkResourcePermission(
        string $permission,
        User $user,
        ?Content $resource = null,
        bool $disallowWiki = false,
        bool $checkViewForum = true
    ): bool {
        if (null === $resource) {
            return false;
        }

        if ($checkViewForum && !$this->viewForum($user)) {
            return false;
        }

        if (!$user->hasPermissionTo($this->type . '.' . $permission)) {
            return false;
        }

        if (!PrivacyPolicy::checkPermission($user, $resource)) {
            return false;
        }

        if ($disallowWiki && $resource->isWiki()) {
            return false;
        }

        return true;
    }

    public function stick(User $user, ?Content $resource = null): bool
    {
        if (!$this->checkResourcePermission('stick', $user, $resource, true)) {
            return false;
        }

        if (!$resource->isApproved()) {
            return false;
        }

        return $this->checkClosedForum($resource);
    }

    public function close(User $user, ?Content $resource = null): bool
    {
        if (null === $resource) {
            return false;
        }

        if ($resource instanceof Content) {
            if (!$resource->isApproved()) {
                return false;
            }
        }

        if ($resource->is_wiki) {
            return false;
        }

        if (!$this->checkClosedForum($resource)) {
            return false;
        }

        if ($user->hasPermissionTo($this->type . '.moderate')) {
            return true;
        }

        if (!$this->viewForum($user)) {
            return false;
        }

        if ($user->entityId() != $resource->userId()) {
            return false;
        }

        if (!$user->hasPermissionTo($this->type . '.close_own')) {
            return false;
        }

        return true;
    }

    public function move(User $user, ?Content $resource = null): bool
    {
        if (null !== $resource) {
            if (!$resource->is_wiki && !$this->checkClosedForum($resource)) {
                return false;
            }

            if ($resource->isWiki()) {
                return false;
            }
        }

        if ($user->hasPermissionTo($this->type . '.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo($this->type . '.move')) {
            return false;
        }

        if ($user->entityId() != $resource->userId()) {
            return false;
        }

        if ($resource instanceof Content) {
            if (!$resource->isApproved()) {
                return false;
            }
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if (null === $resource) {
            return false;
        }

        if ($resource->forum instanceof Forum && $resource->forum->is_closed) {
            return false;
        }

        if ($user->hasPermissionTo($this->type . '.moderate')) {
            return true;
        }

        if (!$this->viewForum($user)) {
            return false;
        }

        return $this->updateOwn($user, $resource);
    }

    public function updateOwn(User $user, ?Content $resource = null): bool
    {
        if ($user->hasPermissionTo($this->type . '.update_own') === true) {
            if ($user->entityId() == $resource->userId()) {
                return true;
            }
        }

        return false;
    }

    public function copy(User $user, ?User $owner, ?Content $resource): bool
    {
        if (null === $resource) {
            return false;
        }

        if (null === $owner) {
            return false;
        }

        if (!$resource->isApproved()) {
            return false;
        }

        if (!$this->viewForum($user)) {
            return false;
        }

        if ($resource->forum instanceof Forum) {
            if ($resource->forum->is_closed) {
                return false;
            }
        }

        if (!$user->hasPermissionTo($this->type . '.copy')) {
            return false;
        }

        if ($owner instanceof User && !PrivacyPolicy::checkPermissionOwner($user, $owner)) {
            return false;
        }

        return true;
    }

    public function merge(User $user, ?Content $resource = null): bool
    {
        if ($resource instanceof Content) {
            if (!$resource->isApproved()) {
                return false;
            }

            if ($resource->is_wiki) {
                return false;
            }

            if (!$this->checkClosedForum($resource)) {
                return false;
            }
        }

        if ($user->hasPermissionTo($this->type . '.moderate')) {
            return true;
        }

        if (!$this->viewForum($user)) {
            return false;
        }

        if (!$user->hasPermissionTo($this->type . '.merge_own')) {
            return false;
        }

        if ($resource instanceof Content) {
            return $resource->userId() == $user->entityId();
        }

        return true;
    }

    public function purchaseSponsor(User $user, ?Content $resource = null): bool
    {
        return false;
    }

    public function approve(User $user, ?Content $resource = null): bool
    {
        if ($user->isGuest()) {
            return false;
        }

        if (null !== $resource) {
            return $this->checkResourcePermission('approve', $user, $resource) && !$resource->isApproved();
        }

        return $user->hasPermissionTo($this->type . '.approve');
    }

    public function attachPoll(User $user, ?Content $resource = null): bool
    {
        if (!$user->hasPermissionTo('forum_thread.attach_poll')) {
            return false;
        }

        if (null === $resource) {
            return true;
        }

        return $this->update($user, $resource);
    }

    public function autoApproved(User $user): bool
    {
        return $this->checkResourcePermission('auto_approved', $user);
    }

    protected function checkClosedForum(Content $resource): bool
    {
        if (null === $resource->forum) {
            return false;
        }

        if ($resource->forum->is_closed) {
            return false;
        }

        return true;
    }
}
