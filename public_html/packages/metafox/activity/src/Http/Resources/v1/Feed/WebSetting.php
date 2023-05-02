<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Activity\Http\Resources\v1\Feed;

use MetaFox\Platform\Resource\WebSetting as Setting;

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
class WebSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('searchItem')
            ->pageUrl('feed/search')
            ->placeholder('Search feeds');

        $this->add('viewAll')
            ->apiUrl('feed')
            ->apiRules([
                'q' => ['truthy', 'q'], 'related_comment_friend_only' => [
                    'or', ['truthy', 'related_comment_friend_only'], ['falsy', 'related_comment_friend_only'],
                ], 'sort' => [
                    'includes', 'sort', ['recent', 'most_viewed', 'most_liked', 'most_discussed'],
                ], 'from' => ['includes', 'from', ['all', 'user', 'page', 'group']],
                'view' => ['truthy', 'view'], 'type_id' => ['truthy', 'type_id'],
            ]);

        $this->add('viewItem')
            ->apiUrl('feed/:id')
            ->pageUrl('feed/:id')
            ->apiParams([
                'comment_id' => ':comment_id',
            ]);

        $this->add('viewOwnerItem')
            ->apiUrl('feed/:feed_id')
            ->apiParams([
                'comment_id' => ':comment_id',
            ]);

        $this->add('editItem')
            ->apiUrl('feed/edit/:id')
            ->asGet();

        $this->add('deleteItem')
            ->apiUrl('feed/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('activity::phrase.delete_confirm'),
                ]
            );

        $this->add('hideItem')
            ->apiUrl('feed/hide-feed/:id')
            ->asPost();

        $this->add('undoHideItem')
            ->apiUrl('feed/hide-feed/:id')
            ->asDelete();

        $this->add('hideAll')
            ->apiUrl('feed/hide-all/:id')
            ->asPost();

        $this->add('undoHideAll')
            ->apiUrl('feed/hide-all/:id')
            ->asDelete();

        $this->add('snooze')
            ->apiUrl('feed/snooze/:id')
            ->asPost();

        $this->add('undoSnooze')
            ->apiUrl('feed/snooze/:id')
            ->asDelete();

        $this->add('approvePending')
            ->apiUrl('feed/approve/:id')
            ->asPatch();

        $this->add('declinePending')
            ->apiUrl('feed/decline/:id')
            ->asPatch();

        $this->add('updatePrivacy')
            ->apiUrl('feed/privacy/:id')
            ->asPatch();

        $this->add('declinePendingAndBlockAuthor')
            ->apiUrl('feed/decline/:id')
            ->asPatch()
            ->apiParams([
                'is_block_author' => 1,
            ]);

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
            ->asDelete();

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

        $this->add('shareOnFriendProfile')
            ->apiUrl('friend/mention')
            ->apiParams([
                'view'             => 'friend',
                'limit'            => 10,
                'share_on_profile' => 1,
            ]);

        $this->add('removeItem')
            ->apiUrl('feed/archive/:id')
            ->asPatch();

        $this->add('checkNew')
            ->apiUrl('feed/check-new')
            ->asGet()
            ->apiParams([
                'last_feed_id'     => ':last_feed_id',
                'last_pin_feed_id' => ':last_pin_feed_id',
            ]);

        $this->add('shareNow')
            ->apiUrl('feed/share')
            ->apiParams([
                'item_id'      => ':item_id',
                'item_type'    => ':item_type',
                'post_content' => ':post_content',
                'post_type'    => ':post_type',
                'privacy'      => ':privacy',
            ])
            ->asPost();

        $this->add('shareToNewsFeed');

        $this->add('copyLink');
    }
}
