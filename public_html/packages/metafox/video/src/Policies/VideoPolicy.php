<?php

namespace MetaFox\Video\Policies;

use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User as User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class VideoPolicy.
 * @SuppressWarnings(PHPMD)
 * @ignore
 */
class VideoPolicy implements ResourcePolicyInterface
{
    use HasPolicyTrait;
    use CheckModeratorSettingTrait;

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if ($user->hasPermissionTo('video.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('video.view')) {
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
        $isApproved = $resource->isApproved();

        if (!$isApproved && $user->isGuest()) {
            return false;
        }

        if ($user->hasPermissionTo('video.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('video.view')) {
            return false;
        }

        $owner = $resource->owner;

        if (!$owner instanceof User) {
            return false;
        }

        if ($this->viewOwner($user, $owner) == false) {
            return false;
        }

        // Check can view on resource.
        if (PrivacyPolicy::checkPermission($user, $resource) == false) {
            return false;
        }

        if (!$resource instanceof HasApprove) {
            return true;
        }

        if ($isApproved) {
            return true;
        }

        if ($user->hasPermissionTo('video.approve')) {
            return true;
        }

        if ($resource->isUser($user)) {
            return true;
        }

        if ($owner instanceof HasPrivacyMember) {
            if ($this->checkModeratorSetting($user, $owner, 'approve_or_deny_post')) {
                return true;
            }
        }

        return false;
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

        if (UserPrivacy::hasAccess($user, $owner, 'video.view_browse_videos') == false) {
            return false;
        }

        return true;
    }

    public function create(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('video.create')) {
            return false;
        }

        if ($owner instanceof User) {
            if ($owner->entityId() != $user->entityId()) {
                // Check can view on owner.
                if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
                    return false;
                }

                if (!UserPrivacy::hasAccess($user, $owner, 'video.share_videos')) {
                    return false;
                }
            }
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('video.moderate')) {
            return true;
        }

        return $this->updateOwn($user, $resource);
    }

    public function updateOwn(User $user, ?Content $resource = null): bool
    {
        if (!$user->hasPermissionTo('video.update')) {
            return false;
        }

        if (null === $resource) {
            return true;
        }

        if ($user->entityId() == $resource->userId()) {
            return true;
        }

        return false;
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('video.moderate')) {
            return true;
        }

        if (!$resource instanceof Content) {
            return false;
        }

        if (!$resource->isApproved()) {
            if ($user->hasPermissionTo('video.approve')) {
                return true;
            }

            if ($resource->isUser($user)) {
                return true;
            }

            return false;
        }

        if ($this->deleteOwn($user, $resource)) {
            return true;
        }

        $owner = $resource->owner;

        if ($owner instanceof HasPrivacyMember) {
            if (!$resource->isApproved()) {
                return $this->checkModeratorSetting($user, $owner, 'approve_or_deny_post');
            }
        }

        return false;
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$user->hasPermissionTo('video.delete')) {
            return false;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return true;
    }

    public function uploadToAlbum(User $context, ?User $owner): bool
    {
        if (null === $owner) {
            return false;
        }

        if (!$this->create($context, $owner)) {
            return false;
        }

        if ($owner instanceof HasPrivacyMember) {
            return UserPrivacy::hasAccess($context, $owner, 'video.share_videos');
        }

        return true;
    }

    public function uploadWithPhoto(User $context, User $owner): bool
    {
        if (!$this->create($context, $owner)) {
            return false;
        }

        if ($owner instanceof HasPrivacyMember) {
            return UserPrivacy::hasAccess($context, $owner, 'video.share_videos');
        }

        return true;
    }
}
