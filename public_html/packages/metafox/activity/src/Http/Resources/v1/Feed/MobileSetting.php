<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Activity\Http\Resources\v1\Feed;

use MetaFox\Platform\Resource\MobileSetting as Setting;

/**
 *--------------------------------------------------------------------------
 * Feed Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class WebSetting.
 */
class MobileSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('searchItem')
            ->pageUrl('feed/search')
            ->placeholder('Search feeds');

        $this->add('viewAll')
            ->apiUrl('feed');

        $this->add('viewItem')
            ->apiUrl('feed/:id')
            ->urlParams(['id' => ':id'])
            ->pageUrl('feed/:id')
            ->apiParams([
                'comment_id' => ':comment_id',
            ]);

        $this->add('editItem')
            ->apiUrl('feed/edit/:id')
            ->urlParams(['id' => ':id'])
            ->asGet();

        $this->add('deleteItem')
            ->apiUrl('feed/:id')
            ->urlParams(['id' => ':id'])
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('activity::phrase.delete_confirm'),
                ]
            );

        $this->add('hideItem')
            ->apiUrl('feed/hide-feed/:id')
            ->urlParams(['id' => ':id'])
            ->asPost();

        $this->add('undoHideItem')
            ->apiUrl('feed/hide-feed/:id')
            ->urlParams(['id' => ':id'])
            ->asDelete();

        $this->add('hideAll')
            ->apiUrl('feed/hide-all/:id')
            ->urlParams(['id' => ':id'])
            ->asPost();

        $this->add('undoHideAll')
            ->apiUrl('feed/hide-all/:id')
            ->urlParams(['id' => ':id'])
            ->asDelete();

        $this->add('snooze')
            ->apiUrl('feed/snooze/:id')
            ->urlParams(['id' => ':id'])
            ->asPost();

        $this->add('undoSnooze')
            ->apiUrl('feed/snooze/:id')
            ->urlParams(['id' => ':id'])
            ->asDelete();

        $this->add('approvePending')
            ->apiUrl('feed/approve/:id')
            ->urlParams(['id' => ':id'])
            ->asPatch();

        $this->add('declinePending')
            ->apiUrl('feed/decline/:id')
            ->urlParams(['id' => ':id'])
            ->asPatch();

        $this->add('removeItem')
            ->apiUrl('feed/archive/:id')
            ->urlParams(['id' => ':id'])
            ->asPatch();

        $this->add('updatePrivacy')
            ->apiUrl('feed/privacy/:id')
            ->urlParams(['id' => ':id'])
            ->asPatch();

        $this->add('declinePendingAndBlockAuthor')
            ->apiUrl('feed/decline/:id')
            ->apiParams([
                'is_block_author' => 1,
            ])
            ->asPatch();

        $this->add('viewPins')
            ->apiUrl('feed/pin')
            ->asGet();

        $this->add('pinHome')
            ->apiUrl('feed/pin/:id/home')
            ->confirm(['message' => __p('activity::web.pin_hom_confirm_desc')])
            ->asPost();

        $this->add('unpinHome')
            ->apiUrl('feed/pin/:id/home')
            ->asDelete();

        $this->add('pinItem')
            ->apiUrl('feed/pin/:id')
            ->asPost();

        $this->add('unpinItem')
            ->apiUrl('feed/unpin/:id')
            ->asDelete();

        $this->add('removeTaggedFriend')
            ->apiUrl('feed/tag/:id')
            ->urlParams(['id' => ':id'])
            ->asDelete();

        $this->add('storeItem')
            ->apiUrl('feed')
            ->asPost();

        $this->add('shareNow')
            ->apiUrl('feed/share')
            ->asPost();

        $this->add('shareToNewsFeed');

        $this->add('copyLink');

        $this->add('searchGlobalPost')
            ->apiUrl(apiUrl('search.index'))
            ->apiParams([
                'view'                        => 'feed',
                'q'                           => ':q',
                'owner_id'                    => ':owner_id',
                'when'                        => ':when',
                'related_comment_friend_only' => ':related_comment_friend_only',
                'is_hashtag'                  => ':is_hashtag',
                'from'                        => ':from',
            ]);

        $this->add('reviewTagStreams')
            ->apiUrl('feed')
            ->apiRules([
                'q' => ['truthy', 'q'], 'related_comment_friend_only' => [
                    'or', ['truthy', 'related_comment_friend_only'], ['falsy', 'related_comment_friend_only'],
                ], 'sort' => [
                    'includes', 'sort', ['recent', 'most_viewed', 'most_liked', 'most_discussed'],
                ], 'from' => ['includes', 'from', ['all', 'user', 'page', 'group']],
                'view' => ['truthy', 'view'], 'type_id' => ['truthy', 'type_id'],
            ])
            ->apiParams(['is_preview_tag' => 1]);

        $this->add('allowed')
            ->apiUrl('feed/allow-preview/:id')
            ->asPatch()
            ->apiParams(['is_allowed' => 1]);

        $this->add('hideOnTimeline')
            ->apiUrl('feed/allow-preview/:id')
            ->asPatch()
            ->apiParams(['is_allowed' => 0]);
    }
}
