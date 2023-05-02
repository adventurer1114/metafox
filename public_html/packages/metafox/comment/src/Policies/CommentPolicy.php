<?php

namespace MetaFox\Comment\Policies;

use Illuminate\Support\Arr;
use MetaFox\Comment\Models\Comment;
use MetaFox\Comment\Models\CommentAttachment;
use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Platform\Contracts\ActionEntity;
use MetaFox\Platform\Contracts\ActionOnResourcePolicyInterface;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class CommentPolicy.
 * @method viewApprove(User $user, ?Content $resource = null)
 */
class CommentPolicy implements
    ActionOnResourcePolicyInterface
{
    use HasPolicyTrait;
    use CheckModeratorSettingTrait;

    protected string $type = Comment::ENTITY_TYPE;

    public function getEntityType(): string
    {
        return Comment::ENTITY_TYPE;
    }

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if ($owner instanceof User) {
            if (!$this->viewOwner($user, $owner)) {
                return false;
            }
        }

        return true;
    }

    public function view(User $user, Entity $resource): bool
    {
        if (!$this->viewApprove($user, $resource)) {
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

        if (!UserPrivacy::hasAccess($user, $owner, 'comment.view_browse_comments')) {
            return false;
        }

        return true;
    }

    public function create(User $user, ?Content $resource = null): bool
    {
        if (!$user->hasPermissionTo('comment.comment')) {
            return false;
        }

        if (!$resource instanceof HasTotalComment) {
            return false;
        }

        $entityPermission = "{$resource->entityType()}.comment";
        if (!$user->hasPermissionTo($entityPermission)) {
            return false;
        }

        if ($resource->ownerId() != $user->entityId()) {
            $owner = $resource->owner;
            if (!PrivacyPolicy::checkCreateOnOwner($user, $owner)) {
                return false;
            }
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('comment.moderate')) {
            return true;
        }

        if (!$resource instanceof ActionEntity) {
            return false;
        }

        if ($this->updateOwnItem($user, $resource)) {
            return true;
        }

        if ($user->hasPermissionTo('comment.update')) {
            return $user->entityId() == $resource->userId();
        }

        return false;
    }

    public function updateOwnItem(User $user, ?Entity $resource = null): bool
    {
        if (!$resource instanceof ActionEntity) {
            return false;
        }
        $item = $resource->item;

        if (!$user->hasPermissionTo('comment.update_on_own_item')) {
            if ($user->hasPermissionTo('comment.update')) {
                return $user->entityId() == $resource->userId();
            }

            return false;
        }

        return $user->entityId() == $item->userId();
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('comment.moderate')) {
            return true;
        }

        if (!$resource instanceof ActionEntity) {
            return false;
        }

        if ($user->entityId() == $resource->userId()) {
            return $user->hasPermissionTo('comment.delete');
        }

        return $this->deleteOwn($user, $resource);
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$resource instanceof ActionEntity) {
            return false;
        }

        if ($user->entityId() == $resource->userId()) {
            return true;
        }

        // todo performance slow.
        $owner = $resource->item?->owner;

        if ($owner instanceof HasPrivacyMember) {
            return $this->checkModeratorSetting($user, $owner, 'remove_post_and_comment_on_post');
        }

        $commentOwner = $resource->owner;

        if (!$commentOwner instanceof User) {
            return false;
        }

        if ($user->entityId() != $commentOwner->entityId()) {
            return false;
        }

        return $user->hasPermissionTo('comment.delete_on_own_item');
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function share(User $user, ?Content $resource = null): bool
    {
        return false;
    }

    public function comment(User $user, ?Content $resource = null): bool
    {
        if ($resource instanceof HasTotalComment) {
            return $this->create($user, $resource);
        }

        return false;
    }

    public function viewHistory(User $user, ?Content $resource = null): bool
    {
        if (!$resource instanceof Comment) {
            return false;
        }

        return $resource->commentHistory()->exists();
    }

    public function hide(User $context, ?Content $resource = null): bool
    {
        if (null == $resource) {
            return false;
        }

        if ($context->hasPermissionTo('comment.moderate')) {
            return false;
        }

        if ($context->entityId() == $resource->userId()) {
            return false;
        }

        if ($context->entityId() == $resource->ownerId()) {
            return false;
        }

        return $context->hasPermissionTo('comment.hide');
    }

    public function hideGlobal(User $context, ?Content $resource = null): bool
    {
        if (null === $resource) {
            return false;
        }

        $user = $resource->user;

        $owner = $resource->owner;

        if (null === $owner) {
            return false;
        }

        if (null === $user) {
            return false;
        }

        if ($user->entityId() == $context->entityId()) {
            return false;
        }

        if ($context->hasPermissionTo('comment.moderate')) {
            return true;
        }

        return $owner->entityId() == $context->entityId();
    }

    public function removeLinkPreview(User $user, Entity $resource): bool
    {
        if (null === $resource->commentAttachment) {
            return false;
        }

        if ($resource->commentAttachment->item_type != CommentAttachment::TYPE_LINK) {
            return false;
        }

        $params = json_decode($resource->commentAttachment->params, true);

        if (!is_array($params)) {
            return false;
        }

        if (Arr::get($params, 'is_hidden')) {
            return false;
        }

        if (!$this->update($user, $resource)) {
            return false;
        }

        return true;
    }
}
