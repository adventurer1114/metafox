<?php

namespace MetaFox\Platform\Support\AppSetting;

use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\HasSponsor;
use MetaFox\Platform\Contracts\HasSponsorInFeed;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\ResourcePermission as ACL;

/**
 * Trait ResourceExtraTrait.
 * @SuppressWarnings(PHPMD.CyclomaticComplexity
 */
trait ResourceExtraTrait
{
    use CheckModeratorSettingTrait;

    /**
     * @param Content $resource
     * @param User    $user
     *
     * @return array<string, bool>
     */
    private function getResourceExtra(Content $resource, User $user): array
    {
        $canApprove = $this->canApprove($user, $resource);

        return [
            ACL::CAN_SEARCH          => true,
            ACL::CAN_VIEW            => $user->can('view', [$resource, $resource]),
            ACL::CAN_LIKE            => $user->can('like', [$resource, $resource]),
            ACL::CAN_SHARE           => $user->can('share', [$resource, $resource]),
            ACL::CAN_DELETE          => $user->can('delete', [$resource, $resource]),
            ACL::CAN_DELETE_OWN      => $user->can('deleteOwn', [$resource, $resource]),
            ACL::CAN_REPORT          => $user->can('reportItem', [$resource, $resource]),
            ACL::CAN_REPORT_TO_OWNER => $user->can('reportToOwner', [$resource, $resource]),
            // todo fail to check create.
            // ACL::CAN_ADD             => $user->can('create', [$resource]),
            ACL::CAN_ADD     => $user->can('create', []),
            ACL::CAN_EDIT    => $user->can('update', [$resource, $resource]),
            ACL::CAN_COMMENT => $user->can('comment', [$resource, $resource]),
            ACL::CAN_PUBLISH => $user->can('publish', [$resource, $resource]),
            ACL::CAN_FEATURE => $resource instanceof HasFeature && $user->can(
                'feature',
                [$resource, $resource, !$resource->is_featured]
            ),
            ACL::CAN_APPROVE => $canApprove,
            ACL::CAN_SPONSOR => $resource instanceof HasSponsor && $user->can(
                'sponsor',
                [$resource, $resource, !$resource->is_sponsor]
            ),
            ACL::CAN_PURCHASE_SPONSOR => $resource instanceof HasSponsor && $user->can(
                'purchaseSponsor',
                [$resource, $resource, !$resource->is_sponsor]
            ),
            ACL::CAN_SPONSOR_IN_FEED => $resource instanceof HasSponsorInFeed && $user->can(
                'sponsorInFeed',
                [$resource, $resource, !$resource->sponsor_in_feed]
            ),
            ACL::CAN_SAVE_ITEM => $user->can('saveItem', [$resource, $resource]),
        ];
    }

    protected function canApprove(User $user, Content $resource): bool
    {
        if ($resource instanceof User) {
            return $user->can('approve', [$resource, $resource]);
        }

        $owner = $resource->owner;

        if ($owner instanceof HasPrivacyMember) {
            return $this->checkModeratorSetting($user, $owner, 'approve_or_deny_post');
        }

        return $user->can('approve', [$resource, $resource]);
    }
}
