<?php

namespace MetaFox\Blog\Policies;

use MetaFox\Blog\Models\Blog as Resource;
use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class BlogPolicy.
 * @SuppressWarnings(PHPMD)
 * @ignore
 * @codeCoverageIgnore
 */
class BlogPolicy implements ResourcePolicyInterface
{
    use HasPolicyTrait;
    use CheckModeratorSettingTrait;

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if ($user->hasPermissionTo('blog.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('blog.view')) {
            return false;
        }

        if ($owner instanceof User) {
            if (!$this->viewOwner($user, $owner)) {
                return false;
            }
        }

        return true;
    }

    public function view(User $user, Entity $resource): bool
    {
        if (!$resource instanceof Resource) {
            return false;
        }

        if ($user->hasPermissionTo('blog.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('blog.view')) {
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
        if (!PrivacyPolicy::checkPermission($user, $resource)) {
            return false;
        }

        if (!$resource->isPublished()) {
            if ($resource->userId() != $user->entityId()) {
                return false;
            }
        }

        // Check setting view on resource.
        if (!$resource->isApproved()) {
            if ($resource->userId() == $user->entityId()) {
                return true;
            }

            if ($user->hasPermissionTo('blog.approve')) {
                return true;
            }

            if ($owner instanceof HasPrivacyMember) {
                return $this->checkModeratorSetting($user, $owner, 'approve_or_deny_post');
            }

            return false;
        }

        return true;
    }

    public function viewOwner(User $user, User $owner): bool
    {
        // Check can view on owner.
        if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
            return false;
        }

        if (!UserPrivacy::hasAccess($user, $owner, 'blog.view_browse_blogs')) {
            return false;
        }

        return true;
    }

    public function create(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('blog.create')) {
            return false;
        }

        if ($owner instanceof User) {
            if ($owner->entityId() != $user->entityId()) {
                if ($owner->entityType() == 'user') {
                    return false;
                }

                // Check can view on owner.
                if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
                    return false;
                }

                if (!PrivacyPolicy::checkCreateOnOwner($user, $owner)) {
                    return false;
                }

                if (!UserPrivacy::hasAccess($user, $owner, 'blog.share_blogs')) {
                    return false;
                }
            }
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if (!$resource instanceof Resource) {
            return false;
        }

        if ($user->hasPermissionTo('blog.moderate')) {
            return true;
        }

        return $this->updateOwn($user, $resource);
    }

    public function updateOwn(User $user, ?Content $resource = null): bool
    {
        if (!$user->hasPermissionTo('blog.update')) {
            return false;
        }

        if (!$resource instanceof Resource) {
            return true;
        }

        if ($user->entityId() != $resource->userId()) {
            return false;
        }

        return true;
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('blog.moderate')) {
            return true;
        }

        if ($resource instanceof Content) {
            if (!$resource->isApproved() && $user->hasPermissionTo('blog.approve')) {
                return true;
            }
        }

        return $this->deleteOwn($user, $resource);
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$resource instanceof Resource) {
            return false;
        }

        if (!$user->hasPermissionTo('blog.delete')) {
            return false;
        }

        if ($user->entityId() != $resource->userId()) {
            return false;
        }

        return true;
    }

    public function publish(User $user, ?Entity $blog): bool
    {
        if (!$blog instanceof Resource) {
            return false;
        }

        if ($blog->isPublished()) {
            return false;
        }

        if ($user->hasPermissionTo('blog.create')) {
            return true;
        }

        return false;
    }
}
