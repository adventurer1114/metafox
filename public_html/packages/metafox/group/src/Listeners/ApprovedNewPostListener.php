<?php

namespace MetaFox\Group\Listeners;

use Illuminate\Support\Facades\Notification;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Notifications\ApproveNewPostNotification;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;

/**
 * Class ApprovedNewPostListener.
 * @ignore
 */
class ApprovedNewPostListener
{
    /**
     * @param  Content $item
     * @param  User    $resource
     * @return void
     */
    public function handle(Content $item, User $resource): void
    {
        if (!$resource instanceof Group) {
            return;
        }

        if (!$item->isApproved()) {
            return;
        }

        $notification = new ApproveNewPostNotification($item);

        $authorizers = $resource->authorizers()
            ->with(['user'])
            ->get();

        foreach ($authorizers as $authorizer) {
            if ($item->userId() == $authorizer->userId()) {
                continue;
            }

            if ($authorizer->user == null) {
                continue;
            }

            $notificationParams = [$authorizer->user, $notification];

            Notification::send(...$notificationParams);
        }
    }
}
