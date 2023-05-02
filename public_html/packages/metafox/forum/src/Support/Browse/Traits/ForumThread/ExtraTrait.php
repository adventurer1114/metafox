<?php

namespace MetaFox\Forum\Support\Browse\Traits\ForumThread;

use MetaFox\Forum\Support\Facades\ForumThread as ForumThreadFacade;
use MetaFox\Platform\ResourcePermission;
use MetaFox\Platform\Traits\Http\Resources\HasExtra;
use MetaFox\Platform\Contracts\Content;

trait ExtraTrait
{
    use HasExtra;

    public function getThreadExtra(): array
    {
        $resource = $this->resource;

        $extra = $this->getExtra();

        unset($extra[ResourcePermission::CAN_COMMENT]);
        unset($extra[ResourcePermission::CAN_PUBLISH]);

        $context = user();

        $customExtra = ForumThreadFacade::getCustomPolicies($context, $resource);

        $permissions = array_merge($extra, $customExtra, [
            ResourcePermission::CAN_SHARE           => $this->canShare(),
            ResourcePermission::CAN_SPONSOR_IN_FEED => $extra[ResourcePermission::CAN_SPONSOR_IN_FEED] && !$resource->is_wiki && $resource->is_approved,
            ResourcePermission::CAN_SPONSOR         => $extra[ResourcePermission::CAN_SPONSOR] && !$resource->is_wiki && $resource->is_approved,
        ]);

        return $permissions;
    }

    protected function canShare(): bool
    {
        if (!$this->resource->isApproved()) {
            return false;
        }

        $context = user();

        if (!$context->hasPermissionTo('forum_thread.share')) {
            return false;
        }

        return true;
    }
}
