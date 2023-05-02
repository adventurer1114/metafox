<?php

namespace MetaFox\Activity\Traits;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Activity\Models\Share;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasBlockedToOwner;
use MetaFox\Platform\Contracts\HasBlockMember;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Traits\Http\Resources\HasExtra;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Trait HasExtra.
 * @property Content $resource
 */
trait FeedExtra
{
    use HasExtra;

    /**
     * @return array<string,           bool>
     * @throws AuthenticationException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getFeedExtra(): array
    {
        $extra = $this->getExtra();

        $feed = $this->resource;

        $item = $this->resource->item;

        $context = user();

        $user = $feed->user;

        $owner = $feed->owner;

        $feedPolicy = resolve('FeedPolicySingleton');

        $isProfileFeed = request()->get('is_profile_feed', null);

        $permissions = [
            'can_pin_item'                 => $feedPolicy->pinItem($context, $feed),
            'can_hide_item'                => $feedPolicy->hideFeed($context, $feed),
            'can_hide_all_user'            => $feedPolicy->hideAll($context, $user, $isProfileFeed),
            'can_snooze_user'              => $feedPolicy->snooze($context, $user, $isProfileFeed),
            'can_remove_tag_friend'        => $feedPolicy->removeTag($feed),
            'can_change_privacy_from_feed' => $feedPolicy->changePrivacyFromFeed($context, $feed),
            'can_view_histories'           => $feedPolicy->viewHistory($context, $feed),
            'can_remove'                   => false,
            'can_hide_all_owner'           => false,
            'can_snooze_owner'             => false,
            'can_review_feed'              => $feedPolicy->reviewTagStreams($context, $feed),
            'can_edit_feed_item'           => $feedPolicy->updateFeedItem($context, $feed),
        ];

        $extraPermissions = app('events')
            ->dispatch('feed.permissions.extra', [$context, $this->resource]);

        if (is_array($extraPermissions)) {
            foreach ($extraPermissions as $extraPermission) {
                if (is_array($extraPermission) && count($extraPermission)) {
                    $permissions = array_merge($permissions, $extraPermission);
                }
            }
        }

        if ($user instanceof User && $owner instanceof User) {
            if ($user->entityId() != $owner->entityId()) {
                $permissions['can_hide_all_owner'] = $feedPolicy->hideAll($context, $owner, $isProfileFeed);
                $permissions['can_snooze_owner']   = $feedPolicy->snoozeOwner($context, $owner, $isProfileFeed);
                $permissions['can_remove']         = $feedPolicy->removeFeed($feed, $context, $owner);
            }

            if ($owner instanceof HasBlockMember) {
                $permissions['can_block'] = $feedPolicy->blockUser($context, $feed);
            }
        }

        if ($item instanceof Share) {
            $item->loadMissing(['item']);
            $content = $item->item;

            if ($content instanceof Content) {
                $sharedUser  = $content->user;
                $sharedOwner = $content->owner;

                // If shared user is different from feed's user and feed's owner.
                if (
                    $sharedUser->entityId() != $user->entityId()
                    && $sharedUser->entityId() != $owner->entityId()
                ) {
                    $permissions['can_hide_all_shared_user'] = $feedPolicy->hideAll(
                        $context,
                        $sharedUser,
                        $isProfileFeed
                    );
                    $permissions['can_snooze_shared_user'] = $feedPolicy->snooze($context, $sharedUser, $isProfileFeed);
                }

                if (
                    // If shared owner is different from feed's user and feed's owner.
                    $sharedOwner->entityId() != $user->entityId()
                    && $sharedOwner->entityId() != $owner->entityId()
                    // And shared owner is different from shared user.
                    && $sharedOwner->entityId() != $sharedUser->entityId()
                ) {
                    $permissions['can_hide_all_shared_owner'] = $feedPolicy->hideAll(
                        $context,
                        $sharedOwner,
                        $isProfileFeed
                    );
                    $permissions['can_snooze_shared_owner'] = $feedPolicy->snooze(
                        $context,
                        $sharedOwner,
                        $isProfileFeed
                    );
                }
            }
        }

        return array_merge($extra, $permissions);
    }
}
