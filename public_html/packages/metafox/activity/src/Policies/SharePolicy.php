<?php

namespace MetaFox\Activity\Policies;

use Illuminate\Support\Arr;
use MetaFox\Activity\Models\Share;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Contracts\User as User;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;

/**
 * Class SharePolicy.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class SharePolicy implements ResourcePolicyInterface
{
    use HasPolicyTrait;

    protected string $type = 'share';

    public function viewAny(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('share.view')) {
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

        //        if (UserPrivacy::hasAccess($user, $owner, 'blog.view_browse_blogs') == false) {
        //            return false;
        //        }

        return true;
    }

    public function view(User $user, Entity $resource): bool
    {
        if (!$user->hasPermissionTo('share.view')) {
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

        // Check setting view on resource.

        return true;
    }

    public function create(User $user, ?User $owner = null): bool
    {
        if (!$user->hasPermissionTo('share.create')) {
            return false;
        }

        if ($owner instanceof User) {
            if ($owner->entityId() != $user->entityId()) {
                if (!policy_check(FeedPolicy::class, 'viewOwner', $user, $owner)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function shareItem(User $user, ?User $owner = null, ?array $attributes = []): bool
    {
        if (!$this->create($user, $owner)) {
            return false;
        }

        if (!Arr::has($attributes, 'item_type')) {
            return false;
        }

        $itemType         = Arr::get($attributes, 'item_type', '');
        $entityPermission = "$itemType.share";

        if (!$user->hasPermissionTo($entityPermission)) {
            return false;
        }

        return true;
    }

    public function update(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('share.moderate')) {
            return true;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return $user->hasPermissionTo('share.update');
    }

    public function delete(User $user, ?Entity $resource = null): bool
    {
        if ($user->hasPermissionTo('share.moderate')) {
            return true;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return $user->hasPermissionTo('share.delete');
    }

    public function deleteOwn(User $user, ?Entity $resource = null): bool
    {
        if (!$user->hasPermissionTo('share.delete')) {
            return false;
        }

        if ($resource instanceof Content) {
            if ($user->entityId() != $resource->userId()) {
                return false;
            }
        }

        return true;
    }

    public function saveItem(User $user, Content $resource = null): bool
    {
        if ($resource instanceof Share) {
            $item = $resource->item;

            if (!$item instanceof Content) {
                return false;
            }

            return PolicyGate::check($item->entityType(), 'saveItem', [$user, $item]);
        }

        return false;
    }

    public function isSavedItem(User $user, Content $resource): bool
    {
        if ($resource instanceof Share) {
            $item = $resource->item;

            if (!$item instanceof Content) {
                return false;
            }

            return PolicyGate::check($item->entityType(), 'isSavedItem', [$user, $item]);
        }

        return false;
    }
}
