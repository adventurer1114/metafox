<?php

namespace MetaFox\Page\Listeners;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Notification;
use MetaFox\Activity\Support\ActivitySubscription;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Notifications\ApproveNewPostNotification;
use MetaFox\Page\Support\Facade\Page as PageFacade;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;
use MetaFox\User\Support\Facades\UserEntity;

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
        if (!$resource instanceof Page) {
            return;
        }

        if (!$item->isApproved()) {
            return;
        }

        $this->handleSendNotificationForFollowers($item, $resource);
    }

    /**
     * @param  Content $item
     * @param  Page    $resource
     * @return void
     */
    protected function handleSendNotificationForFollowers(Content $item, Page $resource): void
    {
        $notification = new ApproveNewPostNotification($item);
        $userItem     = $item->user;
        $followers    = [];

        if (!$resource->isAdmin($userItem)) {
            return;
        }

        $consider    = ['owner_id' => $resource->entityId()];
        $idFollowers = $this->activitySub()->buildSubscriptions($consider)->pluck('user_id')->toArray();

        foreach ($idFollowers as $id) {
            if ($id == $userItem->entityId()) {
                continue;
            }

            $follower    = UserEntity::getById($id)->detail;
            $isFollowing = PageFacade::isFollowing($follower, $resource);

            if (!$isFollowing) {
                continue;
            }
            $followers[] = $follower;
        }

        $notificationParams = [$followers, $notification];
        Notification::send(...$notificationParams);
    }

    /**
     * @return Application|ActivitySubscription|(ActivitySubscription&Application)|mixed
     */
    private function activitySub()
    {
        return resolve('Activity.Subscription');
    }
}
