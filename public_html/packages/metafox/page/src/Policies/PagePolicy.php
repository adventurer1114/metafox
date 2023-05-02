<?php

namespace MetaFox\Page\Policies;

use MetaFox\Page\Models\Page;
use MetaFox\Page\Models\Page as Resource;
use MetaFox\Page\Repositories\BlockRepositoryInterface;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class PagePolicy.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PagePolicy implements ResourcePolicyInterface
{
    use HasPolicyTrait;

    protected string $type = Resource::ENTITY_TYPE;

    public function getEntityType(): string
    {
        return Resource::ENTITY_TYPE;
    }

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if ($user->hasPermissionTo('page.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('page.view')) {
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

        return true;
    }

    public function view(User $user, Entity $resource): bool
    {
        $isApproved = $resource->isApproved();

        if (!$isApproved && $user->isGuest()) {
            return false;
        }

        if ($user->hasPermissionTo('page.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('page.view')) {
            return false;
        }

        if (!$isApproved) {
            if (!$user->hasPermissionTo('page.approve') && $user->entityId() != $resource->userId()) {
                return false;
            }
        }

        $blockRepository = resolve(BlockRepositoryInterface::class);

        if ($blockRepository->isBlocked($resource->entityId(), $user->entityId())) {
            return false;
        }

        return true;
    }

    public function create(User $user, ?User $owner = null): bool
    {
        return $user->hasPermissionTo('page.create');
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if (!$resource instanceof Resource) {
            return false;
        }

        if ($user->hasPermissionTo('page.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('page.update')) {
            return false;
        }

        return $resource->isAdmin($user);
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        // todo check
        if (!$resource instanceof Resource) {
            return false;
        }

        if ($user->hasPermissionTo('page.moderate')) {
            return true;
        }

        if (!$resource->isApproved() && $user->hasPermissionTo('page.approve')) {
            return true;
        }

        return $this->deleteOwn($user, $resource);
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$user->hasPermissionTo('page.delete')) {
            return false;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return true;
    }

    public function share(User $user, ?Content $resource = null): bool
    {
        return $user->hasPermissionTo('page.share');
    }

    /**
     * @param User          $user
     * @param resource|null $resource
     *
     * @return bool
     */
    public function claim(User $user, Resource $resource = null): bool
    {
        if (!$user->hasPermissionTo('page.claim')) {
            return false;
        }

        if (null != $resource) {
            if ($resource->isAdmin($user)) {
                return false;
            }
        }

        return true;
    }

    public function moderate(User $user, Resource $resource = null): bool
    {
        if ($user->hasPermissionTo('page.moderate')) {
            return true;
        }
        if ($resource == null) {
            return false;
        }

        return $resource->isUser($user);
    }

    /**
     * @param User    $user
     * @param Content $resource
     *
     * @return bool
     */
    public function isPageOwner(User $user, Content $resource): bool
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
    public function isPageAdmin(User $user, Content $resource): bool
    {
        if (!$resource instanceof HasPrivacyMember) {
            return false;
        }

        if (!$resource->isAdmin($user)) {
            return false;
        }

        return true;
    }

    public function message(User $user, Resource $resource = null): bool
    {
        return false;

        if ($user->entityId() == $resource->user->entityId()) {
            return false;
        }

        if ($resource instanceof Content) {
            if (!$resource->isApproved()) {
                return false;
            }
        }

        return true;
    }

    public function viewPublishedDate(User $user, ?Resource $page): bool
    {
        if (null === $page) {
            return false;
        }

        return UserPrivacy::hasAccess($user, $page, 'core.view_publish_date');
    }

    public function report(User $user, ?Resource $resource = null): bool
    {
        if (!$resource instanceof Content) {
            return false;
        }
        if (!$resource->isApproved()) {
            return false;
        }

        if ($resource->userId() == $user->entityId()) {
            return false;
        }

        if ($user->hasPermissionTo('page.moderate')) {
            return true;
        }

        return $user->hasPermissionTo('page.report');
    }

    public function inviteFriends(User $user, ?Resource $resource = null): bool
    {
        return !$user->isGuest();
    }

    public function postAsParent(User $user, ?Resource $page): bool
    {
        if (null === $page) {
            return false;
        }

        if ($page->isAdmin($user)) {
            return true;
        }

        return false;
    }

    public function uploadCover(User $user, Page $page): bool
    {
        if (!$user->hasPermissionTo('photo.create')) {
            return false;
        }

        if (!$page->isApproved()) {
            return false;
        }

        return $user->hasPermissionTo('page.upload_cover') && $this->update($user, $page);
    }

    public function editCover(User $user, Page $page): bool
    {
        if (!$this->update($user, $page)) {
            return false;
        }

        if (!$page->isApproved()) {
            return false;
        }

        return $page->cover_id > 0;
    }

    public function follow(User $user, Page $page): bool
    {
        $follow = app('events')->dispatch('follow.can_follow', [$user, $page], true);

        if ($follow == null) {
            return false;
        }

        return $follow;
    }
}
