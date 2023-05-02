<?php

namespace MetaFox\Photo\Policies;

use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Photo\Models\Album;
use MetaFox\Photo\Models\Photo;
use MetaFox\Photo\Policies\Contracts\PhotoPolicyInterface;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Content as Resource;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasAvatarMorph;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * @SuppressWarnings(PHPMD)
 */
class PhotoPolicy implements
    ResourcePolicyInterface,
    PhotoPolicyInterface
{
    use HasPolicyTrait;
    use CheckModeratorSettingTrait;

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if ($user->hasPermissionTo('photo.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('photo.view')) {
            return false;
        }

        if ($owner) {
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

        if ($user->hasPermissionTo('photo.moderate')) {
            return true;
        }

        // Check user role + permission.
        if (!$user->hasPermissionTo('photo.view')) {
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

        // Check setting view on resource.
        if ($isApproved) {
            return true;
        }

        if ($user->hasPermissionTo('photo.approve')) {
            return true;
        }

        if ($owner instanceof HasPrivacyMember) {
            if ($this->checkModeratorSetting($user, $owner, 'approve_or_deny_post')) {
                return true;
            }
        }

        return $user->entityId() == $resource->userId();
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

        if (!UserPrivacy::hasAccess($user, $owner, 'photo.view_browse_photos')) {
            return false;
        }

        return true;
    }

    public function create(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('photo.create')) {
            return false;
        }

        if ($owner) {
            // Check can view on owner.
            if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
                return false;
            }

            if (!UserPrivacy::hasAccess($user, $owner, 'photo.share_photos')) {
                return false;
            }
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('photo.moderate')) {
            return true;
        }

        return $this->updateOwn($user, $resource);
    }

    public function updateOwn(User $user, ?Resource $resource = null): bool
    {
        if (!$user->hasPermissionTo('photo.update')) {
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
        if ($user->hasPermissionTo('photo.moderate')) {
            return true;
        }

        if ($resource instanceof Content) {
            if (!$resource->isApproved() && $user->hasPermissionTo('photo.approve')) {
                return true;
            }
        }

        if (!$resource instanceof Resource) {
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

            return $this->checkModeratorSetting($user, $owner, 'remove_post_and_comment_on_post');
        }

        return false;
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$user->hasPermissionTo('photo.delete')) {
            return false;
        }

        if ($resource instanceof Resource) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return true;
    }

    public function setProfileAvatar(User $user, ?Resource $resource = null): bool
    {
        if (!$resource instanceof Resource) {
            return false;
        }

        if (!$resource->isApproved()) {
            return false;
        }

        $owner = $resource->owner;

        if ($user->hasPermissionTo("{$resource->ownerType()}.moderate")) {
            return true;
        }

        $isResourceUser = $user->entityId() == $resource->userId();
        if ($user->entityId() != $owner?->userId()) {
            return $isResourceUser || false;
        }

        if (!$isResourceUser) {
            return false;
        }

        if (!$user->hasPermissionTo('photo.set_profile_avatar')) {
            return false;
        }

        return true;
    }

    public function setProfileCover(User $user, ?Resource $resource = null): bool
    {
        if (!$resource instanceof Resource) {
            return false;
        }

        if (!$resource->isApproved()) {
            return false;
        }

        $owner = $resource->owner;

        if ($user->hasPermissionTo("{$owner->entityType()}.moderate")) {
            return true;
        }

        $isResourceUser = $user->entityId() == $resource->userId();
        if ($user->entityId() != $owner->userId()) {
            return $isResourceUser;
        }

        if (!$isResourceUser) {
            return false;
        }

        if (!$user->hasPermissionTo('photo.set_profile_cover')) {
            return false;
        }

        return true;
    }

    public function removeProfileCoverOrAvatar(User $user, ?Resource $resource = null): bool
    {
        if (!$resource instanceof Resource) {
            return false;
        }
        $owner = UserEntity::getById($resource->ownerId())->detail;

        if ($user->hasPermissionTo("{$owner->entityType()}.moderate")) {
            return true;
        }

        if ($user->entityId() != $owner->userId()) {
            return false;
        }

        if (!$resource->isApproved()) {
            return false;
        }

        return true;
    }

    public function setParentCover(User $user, ?Resource $resource = null): bool
    {
        if ($resource instanceof Content) {
            if (!$resource->isApproved()) {
                return false;
            }

            $owner = $resource->owner;
            if (!$owner instanceof HasPrivacyMember) {
                return false;
            }

            if ($user->hasPermissionTo("{$owner->entityType()}.moderate")) {
                return true;
            }

            if (!$user->hasPermissionTo("{$owner->entityType()}.upload_cover")) {
                return false;
            }

            if ($resource->ownerId() != $owner->entityId()) {
                return false;
            }

            if (!$owner->isAdmin($user)) {
                return false;
            }
        }

        return true;
    }

    public function download(User $user, ?Resource $resource = null): bool
    {
        if (!$resource instanceof Content) {
            return false;
        }

        if (!$resource instanceof Photo) {
            return false;
        }

        if ($user->entityId() == $resource->userId()) {
            return true;
        }

        if (!$user->hasPermissionTo('photo.download')) {
            return false;
        }

        if (!$resource->isApproved()) {
            return false;
        }

        if (!$this->view($user, $resource)) {
            return false;
        }

        return true;
    }

    public function tagFriend(User $user, ?User $friend = null, ?Resource $resource = null): bool
    {
        if ($friend instanceof User) {
            if (!$this->viewOwner($friend, $user)) {
                return false;
            }
        }

        if ($user->hasPermissionTo('photo.tag_friend_any')) {
            return true;
        }

        if (!$user->hasPermissionTo('photo.tag_friend')) {
            return false;
        }

        if ($resource != null && $resource->userId() != $user->entityId()) {
            return false;
        }

        return true;
    }

    public function viewOnProfilePage(User $user, User $owner): bool
    {
        if (UserPrivacy::hasAccess($user, $owner, 'profile.view_profile') == false) {
            return false;
        }

        if ($owner->entityType() == 'user') {
            if (UserPrivacy::hasAccess($user, $owner, 'photo.display_on_profile') == false) {
                return false;
            }
        }

        return true;
    }

    public function updateAlbum(User $context, ?Resource $content): bool
    {
        if (!$content instanceof Photo) {
            return false;
        }

        if (!$this->update($context, $content)) {
            return false;
        }

        if ($content->album_id == 0) {
            return true;
        }

        $album = $content->album;

        if (!$album instanceof Album) {
            return true;
        }

        return $album->is_normal;
    }

    public function uploadToAlbum(User $context, ?User $owner, ?int $albumId = null): bool
    {
        if (null === $owner) {
            return false;
        }

        if (!PrivacyPolicy::checkPermissionOwner($context, $owner)) {
            return false;
        }

        if ($owner instanceof HasPrivacyMember) {
            return UserPrivacy::hasAccess($context, $owner, 'photo.share_photos');
        }

        if ($albumId == null) {
            return true;
        }
        $album = app('events')->dispatch('photo.album.get_by_id', [$albumId], true);

        if ($album?->is_timeline) {
            return true;
        }

        if ($owner->entityId() == $context->entityId()) {
            return true;
        }

        return false;
    }

    public function setParentAvatar(User $user, ?Resource $resource = null): bool
    {
        if (!$resource instanceof Content) {
            return false;
        }

        if (!$resource->isApproved()) {
            return false;
        }

        $owner = $resource->owner;
        if (!$owner instanceof HasPrivacyMember) {
            return false;
        }

        if (!$owner instanceof HasAvatarMorph) {
            return false;
        }

        if ($user->hasPermissionTo("{$owner->entityType()}.moderate")) {
            return true;
        }

        if ($resource->ownerId() != $owner->entityId()) {
            return false;
        }

        return $owner->isAdmin($user);
    }
}
