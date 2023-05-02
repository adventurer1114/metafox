<?php

namespace MetaFox\Page\Http\Resources\v1\Traits;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Page\Policies\PageMemberPolicy;
use MetaFox\Page\Policies\PagePolicy;
use MetaFox\Page\Support\ResourcePermission;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\ResourcePermission as ACL;

/**
 * Trait PageHasExtra.
 *
 * @property Content $resource
 * @ignore
 * @codeCoverageIgnore
 */
trait PageHasExtra
{
    /**
     * @return array<string,           bool>
     * @throws AuthenticationException
     */
    protected function getExtra(): array
    {
        $policy           = new PagePolicy();
        $pageMemberPolicy = new PageMemberPolicy();
        $context          = user();
        $canView          = $policy->view($context, $this->resource);
        $canUpdate        = $policy->update($context, $this->resource);
        $canUploadCover   = $policy->uploadCover($context, $this->resource);
        $canEditCover     = $policy->editCover($context, $this->resource);

        return [
            ACL::CAN_VIEW                             => $canView,
            ACL::CAN_MODERATE                         => $policy->moderate($context, $this->resource),
            ACL::CAN_LIKE                             => $pageMemberPolicy->likePage($context, $this->resource),
            ACL::CAN_SHARE                            => $policy->share($context, $this->resource),
            ACL::CAN_DELETE                           => $policy->delete($context, $this->resource),
            ACL::CAN_DELETE_OWN                       => $policy->deleteOwn($context, $this->resource),
            ACL::CAN_REPORT                           => $policy->report($context, $this->resource),
            ACL::CAN_ADD                              => $policy->create($context, $this->resource->owner),
            ACL::CAN_EDIT                             => $canUpdate,
            ACL::CAN_COMMENT                          => $policy->comment($context, $this->resource),
            ACL::CAN_FEATURE                          => $policy->feature($context, $this->resource),
            ACL::CAN_APPROVE                          => $policy->approve($context, $this->resource),
            ACL::CAN_SPONSOR                          => $policy->sponsor($context, $this->resource),
            ACL::CAN_SPONSOR_IN_FEED                  => false,
            ACL::CAN_PURCHASE_SPONSOR                 => $policy->purchaseSponsor($context, $this->resource),
            ResourcePermission::CAN_CLAIM             => $policy->claim($context, $this->resource),
            ResourcePermission::CAN_UNLIKE            => $pageMemberPolicy->unlikePage($context, $this->resource),
            ResourcePermission::CAN_VIEW_MEMBER       => $policy->viewAny($context),
            ResourcePermission::CAN_DELETE_MEMBER     => $canUpdate, //@todo: TBD later
            ResourcePermission::CAN_VIEW_INFO         => $canView, //@todo: TBD later
            ResourcePermission::CAN_POST_AS_ADMIN     => $canUpdate, //@todo: TBD later
            ResourcePermission::CAN_EDIT_COVER        => $canEditCover,
            ResourcePermission::CAN_ADD_COVER         => $canUploadCover,
            ResourcePermission::CAN_VIEW_PUBLISH_DATE => $policy->viewPublishedDate($context, $this->resource),
            ResourcePermission::CAN_MESSAGE           => $policy->message($context, $this->resource),
            ResourcePermission::CAN_FOLLOW            => $policy->follow($context, $this->resource),
        ];
    }
}
