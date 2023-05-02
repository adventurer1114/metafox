<?php

namespace MetaFox\Event\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Event\Models\Event;
use MetaFox\Event\Models\HostInvite;
use MetaFox\Event\Models\Invite;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class EventPolicy.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class EventPolicy implements ResourcePolicyInterface
{
    use HasPolicyTrait;
    use HandlesAuthorization;
    use CheckModeratorSettingTrait;

    protected string $type = Event::ENTITY_TYPE;

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if ($user->hasPermissionTo('event.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('event.view')) {
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

        if (!UserPrivacy::hasAccess($user, $owner, 'event.view_browse_events')) {
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

        if ($user->hasPermissionTo('event.moderate')) {
            return true;
        }

        if (!$resource instanceof Event) {
            return false;
        }

        if (!$user->hasPermissionTo('event.view')) {
            return false;
        }

        // Check can view on resource.
        if (PrivacyPolicy::checkPermission($user, $resource) == false) {
            return false;
        }

        if (!$this->viewOwner($user, $resource->owner)) {
            return false;
        }
        // Check setting view on resource.

        if (!$isApproved) {
            if ($user->hasPermissionTo('event.approve')) {
                return true;
            }

            return $resource->userId() == $user->entityId();
        }

        return true;
    }

    public function create(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('event.create')) {
            return false;
        }

        if ($owner instanceof User) {
            if ($owner->entityId() != $user->entityId()) {
                if ($owner->entityType() == 'user') {
                    return false;
                }

                // Check can view on owner.
                if (!PrivacyPolicy::checkCreateOnOwner($user, $owner)) {
                    return false;
                }

                if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
                    return false;
                }

                if (!UserPrivacy::hasAccess($user, $owner, 'event.share_events')) {
                    return false;
                }
            }
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('event.moderate')) {
            return true;
        }

        return $this->updateOwn($user, $resource);
    }

    public function updateOwn(User $user, ?Content $resource = null): bool
    {
        if (!$user->hasPermissionTo('event.update')) {
            return false;
        }

        if (!$resource instanceof Event) {
            return true;
        }

        if ($user->entityId() == $resource->userId()) {
            return true;
        }

        if ($resource->isModerator($user)) {
            return true;
        }

        return false;
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('event.moderate')) {
            return true;
        }

        if ($resource instanceof Content) {
            if (!$resource->isApproved() && $user->hasPermissionTo('event.approve')) {
                return true;
            }
        }

        if ($resource instanceof Event) {
            if (!$resource->isAdmin($user)) {
                return false;
            }
        }

        return $user->hasPermissionTo('event.delete');
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$user->hasPermissionTo('event.delete')) {
            return false;
        }

        if ($resource instanceof Event) {
            if (!$resource->isAdmin($user)) {
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
    public function managePendingPosts(User $user, Content $resource): bool
    {
        if (!$resource instanceof Event) {
            return false;
        }

        if (!$resource->isPendingMode()) {
            return false;
        }

        if ($user->hasPermissionTo('event.moderate')) {
            return true;
        }

        if (UserPrivacy::hasAccess($user, $resource, 'event.manage_pending_post')) {
            return true;
        }

        return false;
    }

    /**
     * @param User    $user
     * @param Content $resource
     *
     * @return bool
     */
    public function createDiscussion(User $user, Content $resource): bool
    {
        if (!$resource->isApproved()) {
            return false;
        }

        if ($user->hasPermissionTo('event.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('event.discussion')) {
            return false;
        }

        if ($resource instanceof User) {
            if (UserPrivacy::hasAccess($user, $resource, 'feed.share_on_wall')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User    $user
     * @param Content $resource
     *
     * @return bool
     */
    public function viewDiscussion(User $user, Content $resource): bool
    {
        if ($user->hasPermissionTo('event.moderate')) {
            return true;
        }

        if ($resource instanceof User) {
            if (UserPrivacy::hasAccess($user, $resource, 'feed.view_wall')) {
                return true;
            }
        }

        return false;
    }

    public function updateRsvp(User $user, ?Content $resource = null): bool
    {
        if (!$resource instanceof Event) {
            return false;
        }

        // Rule: host/co-host can not modify rsvp status
        return !$resource->isModerator($user);
    }

    public function invite(User $user, ?Content $resource = null): bool
    {
        if (!$resource instanceof Event) {
            return false;
        }

        if (!$resource->isApproved()) {
            return false;
        }

        if ($resource->privacy == MetaFoxPrivacy::ONLY_ME) {
            return false;
        }

        if ($user->isGuest()) {
            return false;
        }

        return true;
    }

    public function manageHosts(User $user, ?Content $resource = null): bool
    {
        if (!$resource instanceof Event) {
            return false;
        }

        if ($user->hasPermissionTo('event.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('event.update')) {
            return false;
        }

        if ($resource->isEnded()) {
            return false;
        }

        return $resource->isAdmin($user);
    }

    /**
     * @param User $user
     * @param User $resource
     *
     * @return bool
     */
    public function viewHosts(User $user, User $resource): bool
    {
        if (!$this->view($user, $resource)) {
            return false;
        }

        return UserPrivacy::hasAccess($user, $resource, 'event.view_hosts');
    }

    /**
     * @param User $user
     * @param User $resource
     *
     * @return bool
     */
    public function viewMembers(User $user, User $resource): bool
    {
        if (!$this->view($user, $resource)) {
            return false;
        }

        return UserPrivacy::hasAccess($user, $resource, 'event.view_members');
    }

    /**
     * viewFeedContent.
     *
     * @param  User   $user
     * @param  Event  $event
     * @param  string $status
     * @param  bool   $isYour
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function viewFeedContent(User $user, Event $event, string $status, bool $isYour = false): bool
    {
        if ($event->isAdmin($user)) {
            return true;
        }

        switch ($status) {
            case MetaFoxConstant::ITEM_STATUS_APPROVED:
                $granted = UserPrivacy::hasAccess($user, $event, 'feed.view_wall');
                break;
            case MetaFoxConstant::ITEM_STATUS_PENDING:
                $granted = match ($isYour) {
                    true  => true,
                    false => UserPrivacy::hasAccess($user, $event, 'event.manage_pending_post'),
                };
                break;
            default:
                $granted = false;
                break;
        }

        return $granted;
    }

    public function massEmail(User $user, Event $event): bool
    {
        if (!$user->hasPermissionTo('event.mass_email')) {
            return false;
        }

        if ($user->hasPermissionTo('event.moderate')) {
            return true;
        }

        if ($event->isEnded()) {
            return false;
        }

        return $event->isAdmin($user);
    }

    public function removeInvite(User $user, Invite $invite): bool
    {
        if ($user->entityId() == $invite->userId()) {
            return true;
        }

        return $this->update($user, $invite->event);
    }

    public function removeInviteHost(User $user, HostInvite $invite): bool
    {
        if ($user->entityId() == $invite->userId()) {
            return true;
        }

        return $this->update($user, $invite->event);
    }
}
