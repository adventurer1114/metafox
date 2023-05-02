<?php

namespace MetaFox\Activity\Policies;

use MetaFox\Activity\Contracts\TypeManager;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Stream;
use MetaFox\Activity\Models\Type;
use MetaFox\Activity\Policies\Contracts\HideAllPolicyInterface;
use MetaFox\Activity\Policies\Contracts\HideFeedPolicyInterface;
use MetaFox\Activity\Policies\Traits\CheckPrivacyShareabilityTrait;
use MetaFox\Activity\Policies\Traits\HideAllPolicyTrait;
use MetaFox\Activity\Policies\Traits\HideFeedPolicyTrait;
use MetaFox\Activity\Policies\Traits\PinFeedPolicyTrait;
use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasBlockMember;
use MetaFox\Platform\Contracts\HasItemMorph;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\HasReportToOwner;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\PostBy;
use MetaFox\Platform\Contracts\User as User;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Helpers\IsFriendTrait;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * @SuppressWarnings(PHPMD)
 */
class FeedPolicy implements
    ResourcePolicyInterface,
    HideFeedPolicyInterface,
    HideAllPolicyInterface
{
    use HasPolicyTrait;
    use HideFeedPolicyTrait;
    use PinFeedPolicyTrait;
    use CheckModeratorSettingTrait;
    use CheckPrivacyShareabilityTrait;
    use IsFriendTrait;
    use HideAllPolicyTrait;

    protected string $type = Feed::class;

    private function getTypeManager(): TypeManager
    {
        return resolve(TypeManager::class);
    }

    private function getActionItem(Feed $resource): ?Entity
    {
        return $this->getTypeManager()->hasFeature($resource->type_id, Type::ACTION_ON_FEED_TYPE)
            ? $resource
            : $resource->item;
    }

    public function snooze(User $user, ?User $owner = null, ?bool $isProfileFeed = null): bool
    {
        //TODO: temporarily hide
        return false;
    }

    public function snoozeOwner(User $user, ?User $owner, ?bool $isProfileFeed = null): bool
    {
        //TODO: temporarily hide
        return false;
        // In case already snoozed this owner
        if (!$this->snooze($user, $owner, $isProfileFeed)) {
            return false;
        }

        // In case feed is belonged to page/group/event
        if ($owner->entityType() != $user->entityType()) {
            return false;
        }

        return true;
    }

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if ($user->hasPermissionTo('feed.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('feed.view')) {
            return false;
        }

        if ($owner instanceof User) {
            // Check can view on owner.
            if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
                return false;
            }
        }

        return true;
    }

    public function view(User $user, Entity $resource): bool
    {
        if ($user->hasPermissionTo('feed.moderate')) {
            return true;
        }
        // check user role permission
        if (!$user->hasPermissionTo('feed.view')) {
            return false;
        }

        $owner = $resource->owner;

        // When we view on specific resource, if owner deleted, we cannot see this resource.
        if (!$owner instanceof User) {
            return false;
        }

        if (!$this->viewOwner($user, $owner)) {
            return false;
        }

        $isPublic = $resource->isApproved();

        if (!$isPublic) {
            if ($user->can('update', [$owner, $owner])) {
                return true;
            }

            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        // Check can view on resource.
        if ($resource instanceof HasItemMorph) {
            // @todo check when embed object is null.
            if ($resource->item === null) {
                return false;
            }
            $item = $resource->item;

            if ($item->owner instanceof HasPrivacyMember) {
                return $this->viewContent($user, $item->owner, MetaFoxConstant::ITEM_STATUS_APPROVED);
            }

            if (!PrivacyPolicy::checkPermission($user, $item)) {
                return false;
            }

            if (!PrivacyPolicy::checkPermission($user, $resource)) {
                return false;
            }
        }

        // Check setting view on resource.

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

    public function create(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('feed.create')) {
            return false;
        }

        if ($owner instanceof User) {
            if ($owner->entityId() != $user->entityId()) {
                if (!$this->viewOwner($user, $owner)) {
                    return false;
                }
            }

            if (!PrivacyPolicy::checkCreateOnOwner($user, $owner)) {
                return false;
            }
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if (!$resource instanceof Feed) {
            return false;
        }

        if (!$this->getTypeManager()->hasFeature($resource->type_id, Type::CAN_EDIT_TYPE)) {
            return false;
        }

        if ($user->hasPermissionTo('feed.moderate')) {
            return true;
        }

        if (!$user->hasPermissionTo('feed.update')) {
            return false;
        }

        if ($this->isParentOwner($user, $resource->owner)) {
            return true;
        }

        if ($user->entityId() != $resource->userId()) {
            return false;
        }

        return true;
    }

    public function approve(User $user, ?Content $resource = null): bool
    {
        if ($user->hasPermissionTo('feed.moderate')) {
            return true;
        }

        if ($resource instanceof Content) {
            $owner = $resource->owner;
            if ($owner instanceof HasPrivacyMember) {
                return $this->checkModeratorSetting($user, $owner, 'approve_or_deny_post');
            }
        }

        return true;
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('feed.moderate')) {
            return true;
        }

        if ($this->isParentOwner($user, $resource->owner)) {
            return true;
        }

        return $this->deleteOwn($user, $resource);
    }

    protected function isParentOwner(User $user, ?User $owner): bool
    {
        if (!$owner instanceof HasPrivacyMember) {
            return false;
        }

        if (!method_exists($owner, 'isOwner')) {
            return false;
        }

        if (true === call_user_func([$owner, 'isOwner'], $user)) {
            return true;
        }

        return false;
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$user->hasPermissionTo('feed.delete')) {
            return false;
        }

        if ($resource instanceof Content) {
            $owner = $resource->owner;

            if (null === $owner) {
                return false;
            }

            if (method_exists($owner, 'hasDeleteFeedPermission')) {
                return call_user_func([$owner, 'hasDeleteFeedPermission'], $user, $resource);
            }

            if ($user->entityId() != $resource->userId()) {
                if ($owner instanceof HasPrivacyMember) {
                    return $this->checkModeratorSetting($user, $owner, 'remove_post_and_comment_on_post');
                }

                if ($owner->entityId() == $user->entityId()) {
                    return true;
                }

                if ($owner->userId() == $user->entityId()) {
                    return true;
                }

                return false;
            }
        }

        return true;
    }

    public function like(User $user, ?Content $resource = null): bool
    {
        if (!app_active('metafox/like')) {
            return false;
        }

        if (!$resource instanceof Feed) {
            return false;
        }

        $item = $this->getActionItem($resource);

        if (!$item instanceof HasTotalLike) {
            return false;
        }

        $resourceOwner = $resource->owner;

        if (!$resourceOwner instanceof User) {
            return false;
        }

        if (!$resourceOwner->isApproved()) {
            return false;
        }

        // Check permission on Like app before checking with entity
        if (!$user->hasPermissionTo('like.create')) {
            return false;
        }

        if (!$this->getTypeManager()->hasFeature($resource->type_id, Type::CAN_LIKE_TYPE)) {
            return false;
        }

        if (app('events')->dispatch('like.owner.can_like_item', [$user, $resourceOwner], true)) {
            return true;
        }

        return $this->checkCreateOnOwner($user, $resourceOwner);
    }

    public function share(User $user, ?Content $resource = null): bool
    {
        if (!$resource instanceof Feed) {
            return false;
        }

        if (!$this->isPrivacyShareable($resource->privacy)) {
            return false;
        }

        $item = $this->getActionItem($resource);

        if (!$item instanceof HasTotalShare) {
            return false;
        }

        $resourceOwner = $resource->owner;

        if (!$resourceOwner instanceof User) {
            return false;
        }

        if (!$resourceOwner->isApproved()) {
            return false;
        }

        if (!$user->hasPermissionTo('share.create')) {
            return false;
        }

        if (!$this->getTypeManager()->hasFeature($resource->type_id, Type::CAN_SHARE_TYPE)) {
            return false;
        }

        if ($resourceOwner instanceof PostBy) {
            return $resourceOwner->checkContentShareable($user, $resourceOwner);
        }

        return true;
    }

    public function comment(User $user, ?Content $resource = null): bool
    {
        if (!$resource instanceof Feed) {
            return false;
        }

        $item = $this->getActionItem($resource);

        if (!$item instanceof HasTotalComment) {
            return false;
        }

        $resourceOwner = $resource->owner;

        if (!$resourceOwner instanceof User) {
            return false;
        }

        if (!$resourceOwner->isApproved()) {
            return false;
        }

        // User of this role can comment
        if (!$user->hasPermissionTo('comment.comment')) {
            return false;
        }

        if (!$this->getTypeManager()->hasFeature($resource->type_id, Type::CAN_COMMENT_TYPE)) {
            return false;
        }

        if (app('events')->dispatch('comment.owner.can_comment_item', [$user, $resourceOwner], true)) {
            return true;
        }

        if (!$user->can('view', [$item, $item])) {
            return false;
        }

        return $this->checkCreateOnOwner($user, $resourceOwner);
    }

    private function checkCreateOnOwner(User $user, User $owner): bool
    {
        if ($owner->entityId() == $user->entityId()) {
            return true;
        }

        return PrivacyPolicy::checkCreateOnOwner($user, $owner);
    }

    public function reportItem(User $user, Content $resource = null): bool
    {
        if (!$resource instanceof Feed) {
            return false;
        }

        $resourceOwner = $resource->owner;

        $feedUser = $resource->user;

        if ($feedUser instanceof User) {
            if ($feedUser->entityId() == $user->entityId()) {
                return false;
            }
        }

        if ($resourceOwner instanceof HasReportToOwner) {
            return $resourceOwner->canReportItem($user, $resource);
        }

        return $user->hasPermissionTo('feed.report');
    }

    public function reportToOwner(User $user, Content $resource = null): bool
    {
        if (!$resource instanceof Feed) {
            return false;
        }

        $feedUser = $resource->user;

        if ($feedUser instanceof User) {
            if ($feedUser->entityId() == $user->entityId()) {
                return false;
            }
        }

        $resourceOwner = $resource->owner;

        if ($resourceOwner instanceof HasReportToOwner) {
            return $resourceOwner->canReportToOwner($user, $resource);
        }

        return false;
    }

    public function saveItem(User $user, Content $resource = null): bool
    {
        if (!$resource instanceof Feed) {
            return false;
        }

        $resourceOwner = $resource->owner;

        if (!$resourceOwner instanceof User) {
            return false;
        }

        if (!$this->checkCreateOnOwner($user, $resourceOwner)) {
            return false;
        }
        $item = $this->getActionItem($resource);

        if ($item instanceof Feed) {
            return false;
        }

        if (!$item instanceof Content) {
            return false;
        }

        return PolicyGate::check($item->entityType(), 'saveItem', [$user, $item]);
    }

    public function isSavedItem(User $user, Content $resource = null): bool
    {
        if (!$resource instanceof Feed) {
            return false;
        }

        $item = $this->getActionItem($resource);

        if ($item instanceof Feed) {
            return false;
        }

        if (!$item instanceof Content) {
            return false;
        }

        return PolicyGate::check($item->entityType(), 'isSavedItem', [$user, $item]);
    }

    public function viewOnProfilePage(User $user, User $owner): bool
    {
        if (!UserPrivacy::hasAccess($user, $owner, 'profile.view_profile')) {
            return false;
        }

        if (!UserPrivacy::hasAccess($user, $owner, 'feed.view_wall')) {
            return false;
        }

        return PolicyGate::check($owner, 'view', [$user, $owner]);
    }

    public function sponsor(User $user, ?Content $resource = null): bool
    {
        // if ($resource instanceof Content) {
        //     if ($user->entityId() != $resource->userId()) {
        //         if (!$user->hasPermissionTo('feed.can_sponsor_feed')) {
        //             return false;
        //         }
        //     }
        // }

        // return $this->purchaseSponsor($user, $resource);

        return true;
    }

    public function purchaseSponsor(User $user, ?Content $resource = null): bool
    {
        //        return $user->hasPermissionTo('feed.can_purchase_sponsor');

        return false;
    }

    public function removeTag(?Feed $resource = null): bool
    {
        if (!$resource instanceof Feed) {
            return false;
        }

        $context = user();
        $tags    = $this->getTaggedFriend($resource->item, $context);

        if (empty($tags)) {
            return false;
        }

        return true;
    }

    public function changePrivacyFromFeed(User $user, Feed $feed): bool
    {
        if ($feed->userId() !== $feed->ownerId()) {
            return false;
        }

        if ($user->entityId() !== $feed->userId()) {
            return false;
        }

        /*
         * an activity type need to declare Type::CAN_CHANGE_PRIVACY_FROM_FEED_TYPE => true in its
         * PackageSettingListener::getActivityTypes method for this permission
         */
        if ($this->getTypeManager()->hasFeature($feed->type_id, Type::CAN_CHANGE_PRIVACY_FROM_FEED_TYPE)) {
            return true;
        }

        return false;
    }

    public function viewContent(User $user, User $owner, string $status, bool $isYour = false): bool
    {
        $className = get_class($owner);

        $policy = PolicyGate::getPolicyFor($className);

        if (method_exists($policy, 'viewFeedContent')) {
            return $policy->viewFeedContent($user, $owner, $status, $isYour);
        }

        return true;
    }

    public function archive(User $user, Feed $resource): bool
    {
        if ($resource->is_removed) {
            return false;
        }

        return $this->delete($user, $resource);
    }

    public function removeFeed(Feed $resource, User $user, User $owner): bool
    {
        if (!$owner instanceof PostBy) {
            return false;
        }

        if ($resource->is_removed) {
            return false;
        }

        if (!$resource->isApproved()) {
            return false;
        }

        if ($resource->userId() == $user->entityId()) {
            return false;
        }

        return $owner->hasRemoveFeed($user, $resource);
    }

    public function viewHistory(User $user, Content $resource = null): bool
    {
        if (!$resource instanceof Feed) {
            return false;
        }

        if (!$this->view($user, $resource)) {
            return false;
        }

        return $resource->history()->exists();
    }

    public function reviewTagStreams(User $user, Content $resource = null): bool
    {
        if (!$resource instanceof Feed) {
            return false;
        }

        return $resource->stream()->where('owner_id', $user->entityId())
            ->where('status', Stream::STATUS_ALLOW)
            ->exists();
    }

    public function blockUser(User $context, Content $resource): bool
    {
        $user  = $resource?->user;
        $owner = $resource?->owner;
        if ($user == null || $owner == null) {
            return true;
        }
        if ($owner instanceof HasBlockMember) {
            return $owner->canBlock($context, $user, $owner);
        }

        return true;
    }

    public function updateFeedItem(User $context, Content $resource): bool
    {
        if (!$resource instanceof Feed) {
            return false;
        }

        if ($this->getTypeManager()->hasFeature($resource->type_id, Type::PREVENT_EDIT_FEED_ITEM_TYPE)) {
            return false;
        }

        return $this->update($context, $resource);
    }

    public function pinItem(User $context, Content $resource): bool
    {
        if (!$context->hasPermissionTo('feed.pin')) {
            return false;
        }

        if (null === $resource->owner) {
            return false;
        }

        $owner = $resource->owner;

        if (!$owner instanceof HasPrivacyMember) {
            return $context->entityId() == $owner->entityId();
        }

        if ($context->hasPermissionTo(sprintf('%s.moderate', $owner->entityType()))) {
            return true;
        }

        if ($owner->isAdmin($context)) {
            return true;
        }

        return false;
    }
}
