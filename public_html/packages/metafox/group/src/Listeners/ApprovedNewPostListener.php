<?php

namespace MetaFox\Group\Listeners;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Notification;
use MetaFox\Activity\Support\ActivitySubscription;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Notifications\ApproveNewPostNotification;
use MetaFox\Group\Support\Facades\Group as GroupFacade;
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

            $isFollowing = GroupFacade::isFollowing($authorizer->user, $resource);

            if (!$isFollowing) {
                continue;
            }

            $notificationParams = [$authorizer->user, $notification];

            Notification::send(...$notificationParams);
        }

        $this->handleSendNotificationForFollowers($item, $resource);
    }

    /**
     * @param  Content $item
     * @param  Group   $resource
     * @return void
     */
    protected function handleSendNotificationForFollowers(Content $item, Group $resource): void
    {
        $notification = new ApproveNewPostNotification($item);
        $userItem     = $item->user;
        $followers    = [];

        if (!$resource->isModerator($userItem) && !$resource->isAdmin($userItem)) {
            return;
        }

        $consider    = ['owner_id' => $resource->entityId()];
        $idFollowers = $this->activitySub()->buildSubscriptions($consider)->pluck('user_id')->toArray();

        foreach ($idFollowers as $id) {
            if ($id == $userItem->entityId()) {
                continue;
            }

            $follower    = UserEntity::getById($id)->detail;
            $isFollowing = GroupFacade::isFollowing($follower, $resource);

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
