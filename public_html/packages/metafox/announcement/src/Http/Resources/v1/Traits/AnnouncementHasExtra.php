<?php

namespace MetaFox\Announcement\Http\Resources\v1\Traits;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\ResourcePermission as ACL;

/**
 * @property Content $resource
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
trait AnnouncementHasExtra
{
    /**
     * @return array<string,           bool>
     * @throws AuthenticationException
     */
    protected function getAnnouncementExtra(): array
    {
        $context  = user();
        $resource = $this->resource;

        if (!$resource instanceof Content) {
            return [];
        }

        return [
            ACL::CAN_VIEW    => $context->can('view', [$resource, $resource]),
            ACL::CAN_LIKE    => $context->can('like', [$resource, $resource]),
            ACL::CAN_COMMENT => $context->can('comment', [$resource, $resource]),
        ];
    }
}
