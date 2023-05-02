<?php

namespace MetaFox\Group\Support\Browse\Traits\Group;

use Illuminate\Auth\AuthenticationException;

trait StatisticTrait
{
    /**
     * @throws AuthenticationException
     */
    public function getStatistic(): array
    {
        $totalMembers = null;

        if (user()->hasPermissionTo('group_member.view')) {
            $totalMembers = $this->resource->total_member;
        }

        $totalPendingRequests = 0;

        if (is_numeric($this->resource->pending_requests_count)) {
            $totalPendingRequests = $this->resource->pending_requests_count;
        }

        return [
            'total_member'           => $totalMembers,
            'total_pending_requests' => $totalPendingRequests,
            'total_admin'            => $this->resource->total_admin,
        ];
    }
}
