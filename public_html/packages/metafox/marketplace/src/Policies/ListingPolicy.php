<?php

namespace MetaFox\Marketplace\Policies;

use MetaFox\Marketplace\Models\Listing;
use MetaFox\Marketplace\Support\Facade\Listing as Facade;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User as User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Support\Facades\UserBlocked;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class ListingPolicy.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 */
class ListingPolicy implements ResourcePolicyInterface
{
    use HasPolicyTrait;

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if ($user->hasPermissionTo('marketplace.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('marketplace.view')) {
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

        if ($user->hasPermissionTo('marketplace.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('marketplace.view')) {
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

        if (!$isApproved) {
            if ($user->hasPermissionTo('marketplace.approve')) {
                return true;
            }

            if ($user->entityId() == $resource->userId()) {
                return true;
            }

            return false;
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

        if (!UserPrivacy::hasAccess($user, $owner, 'marketplace.view_browse_marketplace_listings')) {
            return false;
        }

        return true;
    }

    public function create(User $user, ?User $owner = null): bool
    {
        if ($user->hasPermissionTo('marketplace.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('marketplace.create')) {
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

                if (!UserPrivacy::hasAccess($user, $owner, 'marketplace.share_marketplace_listings')) {
                    return false;
                }
            }
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('marketplace.moderate')) {
            return true;
        }

        if (!$this->updateOwn($user, $resource)) {
            return false;
        }

        return true;
    }

    public function updateOwn(User $user, ?Content $resource = null): bool
    {
        if (!$user->hasPermissionTo('marketplace.update')) {
            return false;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return true;
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('marketplace.moderate')) {
            return true;
        }

        if ($resource instanceof Listing) {
            if (!$resource->isApproved() && $user->hasPermissionTo('marketplace.approve')) {
                return true;
            }
        }

        if (!$this->deleteOwn($user, $resource)) {
            return false;
        }

        return true;
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$user->hasPermissionTo('marketplace.delete')) {
            return false;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return true;
    }

    public function payment(User $user, ?Listing $listing): bool
    {
        if (null === $listing) {
            return false;
        }

        if ($user->isGuest()) {
            return false;
        }

        if (!$this->view($user, $listing)) {
            return false;
        }

        if ($user->entityId() == $listing->userId()) {
            return false;
        }

        if (!$listing->isApproved()) {
            return false;
        }

        if ($listing->is_sold) {
            return false;
        }

        if (Facade::isExpired($listing)) {
            return false;
        }

        if (!$listing->allow_payment && !$listing->allow_point_payment) {
            return false;
        }

        if (!$listing->user->hasPermissionTo('marketplace.sell_items')) {
            return false;
        }

        $prices = $listing->price;

        if (!count($prices)) {
            return false;
        }

        $price = Facade::getUserPrice($user, $prices);

        if (null === $price) {
            return false;
        }

        if ($price == 0) {
            return false;
        }

        return true;
    }

    public function invite(User $user, ?Listing $listing): bool
    {
        if (null === $listing) {
            return false;
        }

        if (!$listing->isApproved()) {
            return false;
        }

        if ($listing->is_sold) {
            return false;
        }

        if (Facade::isExpired($listing)) {
            return false;
        }

        if ($listing->privacy == MetaFoxPrivacy::ONLY_ME) {
            return false;
        }

        $owner = $listing->user;

        if (null === $owner) {
            return false;
        }

        if ($owner->entityId() != $user->entityId()) {
            return false;
        }

        return true;
    }

    public function message(User $user, ?Listing $listing): bool
    {
        if (null === $listing) {
            return false;
        }

        if ($user->isGuest()) {
            return false;
        }

        $owner = $listing->user;

        if (null == $owner) {
            return false;
        }

        if ($user->entityId() == $owner->entityId()) {
            return false;
        }

        if (UserBlocked::isBlocked($user, $owner)) {
            return false;
        }

        if (UserBlocked::isBlocked($owner, $user)) {
            return false;
        }

        if ($this->checkChatplusActive($user)) {
            return true;
        }

        if ($this->checkChatActive($user)) {
            return true;
        }

        $active = app('events')->dispatch('message.active', [$user], true);

        if (is_bool($active)) {
            return $active;
        }

        return false;
    }

    protected function checkChatplusActive(User $user): bool
    {
        $response = app('events')->dispatch('chatplus.message.active', [$user], true);

        if (is_bool($response)) {
            return $response;
        }

        if (!app_active('metafox/chatplus')) {
            return false;
        }

        if (!Settings::get('chatplus.server')) {
            return false;
        }

        return true;
    }

    protected function checkChatActive(User $user): bool
    {
        $response = app('events')->dispatch('chat.message.active', [$user], true);

        if (is_bool($response)) {
            return $response;
        }

        if (!app_active('metafox/chat')) {
            return false;
        }

        if (!Settings::get('broadcast.connections.pusher.key')) {
            return false;
        }

        return true;
    }

    public function reopen(User $user, ?Listing $listing): bool
    {
        if (null === $listing) {
            return false;
        }

        if (!Facade::isExpired($listing)) {
            return false;
        }

        if ($user->hasPermissionTo('marketplace.reopen_expired')) {
            return true;
        }

        if (!$this->reopenOwn($user, $listing)) {
            return false;
        }

        return true;
    }

    public function reopenOwn(User $user, ?Listing $listing): bool
    {
        if (!$user->hasPermissionTo('marketplace.reopen_own_expired')) {
            return false;
        }

        if ($listing instanceof Content) {
            if ($user->entityId() != $listing->userId()) {
                return false;
            }
        }

        return true;
    }

    public function viewExpire(User $user, User $owner, int $profileId): bool
    {
        if (0 === $profileId) {
            return $this->hasModerateViewExpiredPermission($user);
        }

        if (!$owner instanceof HasPrivacyMember) {
            if ($owner->entityId() == $user->entityId()) {
                return true;
            }

            return $this->hasModerateViewExpiredPermission($user);
        }

        if ($owner->isAdmin($user)) {
            return true;
        }

        if ($user->hasPermissionTo($owner->entityType() . '.moderate')) {
            return true;
        }

        return false;
    }

    public function hasModerateViewExpiredPermission(User $user): bool
    {
        if ($user->hasPermissionTo('marketplace.moderate')) {
            return true;
        }

        if ($user->hasPermissionTo('marketplace.view_expired')) {
            return true;
        }

        return false;
    }
}
