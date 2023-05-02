<?php

namespace MetaFox\Forum\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use MetaFox\Forum\Models\ForumThread;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class ForumPostPolicy.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ForumPostPolicy implements ResourcePolicyInterface
{
    use HandlesAuthorization;
    use HasPolicyTrait;

    protected string $type = 'forum_post';

    public function create(User $user, ?User $owner = null): bool
    {
        return false;
    }

    public function viewAny(User $user, ?Content $thread = null): bool
    {
        if (!$this->viewForum($user)) {
            return false;
        }

        if (!$this->checkViewThreadPermission($user, $thread)) {
            return false;
        }

        return true;
    }

    private function viewForum(User $user)
    {
        if ($user->hasPermissionTo('forum_thread.moderate')) {
            return true;
        }

        return policy_check(ForumPolicy::class, 'viewAny', $user);
    }

    private function checkViewThreadPermission(User $user, ?Content $thread = null): bool
    {
        if ($thread instanceof ForumThread) {
            return policy_check(ForumThreadPolicy::class, 'view', $user, $thread);
        }

        return true;
    }

    public function view(User $user, Entity $resource = null): bool
    {
        if (null === $resource) {
            return false;
        }

        if (!$this->checkViewThreadPermissionForResource($user, $resource)) {
            return false;
        }

        if (!PrivacyPolicy::checkPermission($user, $resource)) {
            return false;
        }

        return true;
    }

    private function checkViewThreadPermissionForResource(User $user, ?Content $resource = null): bool
    {
        if (!$this->viewForum($user)) {
            return false;
        }

        if ($resource instanceof Content) {
            $thread = $resource->thread;

            if (!$this->checkViewThreadPermission($user, $thread)) {
                return false;
            }
        }

        return true;
    }

    public function reply(User $user, ?Content $thread, bool $isCloned = false): bool
    {
        if (!$this->viewForum($user)) {
            return false;
        }

        if ($isCloned) {
            return true;
        }

        if (!$this->checkViewThreadPermission($user, $thread)) {
            return false;
        }

        if ($thread->is_closed || $thread->is_wiki) {
            return false;
        }

        if (null === $thread->forum) {
            return false;
        }

        if ($thread->forum->is_closed) {
            return false;
        }

        if ($thread instanceof Content) {
            if (!$thread->isApproved()) {
                return false;
            }
        }

        if ($user->hasPermissionTo('forum_thread.moderate')) {
            return true;
        }

        if ($user->hasPermissionTo($this->type . '.reply')) {
            return true;
        }

        return $this->replyOwn($user, $thread);
    }

    public function replyOwn(User $user, ?Content $thread): bool
    {
        if (!$this->viewForum($user)) {
            return false;
        }

        if (!$this->checkViewThreadPermission($user, $thread)) {
            return false;
        }

        if (!$user->hasPermissionTo($this->type . '.reply_own')) {
            return false;
        }

        if ($user->entityId() != $thread->userId()) {
            return false;
        }

        if (!UserPrivacy::hasAccess($user, $thread->owner, 'forum.reply_forum_thread')) {
            return false;
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if (!$this->checkViewThreadPermissionForResource($user, $resource)) {
            return false;
        }

        if (!$resource->thread->is_wiki) {
            if (null === $resource->thread->forum) {
                return false;
            }

            if ($resource->thread->forum->is_closed) {
                return false;
            }
        }

        if ($this->canModerate($user)) {
            return true;
        }

        if (!$user->hasPermissionTo($this->type . '.update_own')) {
            return false;
        }

        return $user->entityId() == $resource->userId();
    }

    public function updateOwn(User $user, ?Content $resource = null): bool
    {
        if ($this->checkViewThreadPermissionForResource($user, $resource) === false) {
            return false;
        }

        if ($resource->userId() != $user->entityId()) {
            return false;
        }

        return $user->hasPermissionTo($this->type . '.update_own');
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if (!$this->checkViewThreadPermissionForResource($user, $resource)) {
            return false;
        }

        if ($this->canModerate($user)) {
            return true;
        }

        return $this->deleteOwn($user, $resource);
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$this->checkViewThreadPermissionForResource($user, $resource)) {
            return false;
        }

        if ($resource->userId() != $user->entityId()) {
            return false;
        }

        return $user->hasPermissionTo($this->type . '.delete_own');
    }

    public function autoApproved(User $user, ?Content $thread = null): bool
    {
        if ($this->checkViewThreadPermission($user, $thread) === false) {
            return false;
        }

        return $user->hasPermissionTo($this->type . '.auto_approved');
    }

    public function approve(User $user): bool
    {
        if ($user->isGuest()) {
            return false;
        }

        return $user->hasPermissionTo($this->type . '.approve');
    }

    public function quote(User $user, ?Content $resource): bool
    {
        if (null === $resource) {
            return false;
        }

        if (!$this->view($user, $resource)) {
            return false;
        }

        if ($resource instanceof Content) {
            if (!$resource->isApproved()) {
                return false;
            }
        }

        $thread = $resource->thread;

        if (null === $thread) {
            return false;
        }

        if ($thread->is_closed) {
            return false;
        }

        if (!$thread->is_wiki) {
            if (null === $thread->forum) {
                return false;
            }

            if ($thread->forum->is_closed) {
                return false;
            }
        }

        if (!$this->reply($user, $thread)) {
            return false;
        }

        return $user->hasPermissionTo($this->type . '.quote');
    }

    public function viewOwner(User $user, ?User $owner = null): bool
    {
        return false;
    }

    //TODO
    public function viewOnProfilePage(User $context, User $owner): bool
    {
        return true;
    }

    public function canModerate(User $user): bool
    {
        return $user->hasPermissionTo('forum_thread.moderate');
    }
}
