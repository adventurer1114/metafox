<?php

namespace MetaFox\Comment\Support\Traits;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Comment\Models\Comment;
use MetaFox\Comment\Policies\CommentPolicy;
use MetaFox\Comment\Support\ResourcePermission;
use MetaFox\Platform\ResourcePermission as ACL;

/**
 * Trait CommentExtraTrait.
 * @property Comment $resource
 */
trait HasCommentExtraTrait
{
    /**
     * @return array<string,           bool>
     * @throws AuthenticationException
     */
    protected function getExtra(): array
    {
        $policy = new CommentPolicy();

        $context = user();

        return [
            ACL::CAN_VIEW                               => $policy->view($context, $this->resource),
            ACL::CAN_ADD                                => $policy->create($context, $this->resource->item),
            ACL::CAN_EDIT                               => $policy->update($context, $this->resource),
            ACL::CAN_DELETE                             => $policy->delete($context, $this->resource),
            ACL::CAN_LIKE                               => $policy->like($context, $this->resource->item),
            ACL::CAN_APPROVE                            => $policy->approve($context, $this->resource),
            ACL::CAN_REPORT                             => $policy->reportItem($context, $this->resource),
            ACL::CAN_COMMENT                            => $policy->comment($context, $this->resource->item),
            ResourcePermission::CAN_HIDE                => $policy->hide($context, $this->resource),
            ResourcePermission::CAN_HIDE_GLOBAL         => $policy->hideGlobal($context, $this->resource),
            ResourcePermission::CAN_VIEW_HISTORIES      => $policy->viewHistory($context, $this->resource),
            ResourcePermission::CAN_REMOVE_LINK_PREVIEW => $policy->removeLinkPreview($context, $this->resource),
        ];
    }
}
