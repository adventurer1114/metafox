<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Video\Http\Resources\v1\Video;

use MetaFox\Platform\Resource\MobileSetting as Setting;
use MetaFox\Platform\Support\Browse\Browse;

/**
 *--------------------------------------------------------------------------
 * Video Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class WebSetting.
 * @ignore
 * @codeCoverageIgnore
 *
 * @driverType resource-mobile
 * @driverName video
 */
class MobileSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('searchItem')
            ->apiUrl('video')
            ->apiParams([
                'q'           => ':q',
                'sort'        => ':sort',
                'when'        => ':when',
                'category_id' => ':category_id',
                'view'        => 'search',
            ])
            ->placeholder(__p('video::phrase.search_videos'));

        $this->add('viewAll')
            ->apiUrl('video')
            ->apiRules([
                'q' => ['truthy', 'q'], 'sort' => [
                    'includes', 'sort', [
                        Browse::SORT_RECENT, Browse::SORT_MOST_LIKED, Browse::SORT_MOST_VIEWED, Browse::SORT_MOST_DISCUSSED,
                        Browse::SORT_A_TO_Z, Browse::SORT_Z_TO_A,
                    ],
                ], 'category_id' => ['truthy', 'category_id'], 'when' => [
                    'includes', 'when',
                    [Browse::WHEN_ALL, Browse::WHEN_THIS_MONTH, Browse::WHEN_THIS_WEEK, Browse::WHEN_TODAY],
                ], 'view' => ['includes', 'view', ['my', 'friend', 'pending']],
            ]);

        $this->add('viewItem')
            ->apiUrl('video/:id')
            ->pageUrl('video/play/:id');

        $this->add('deleteItem')
            ->apiUrl('video/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('video::phrase.delete_confirm'),
                ]
            );

        $this->add('addItem')
            ->pageUrl('video/share')
            ->apiUrl('core/mobile/form/video.video.upload')
            ->apiParams(['owner_id' => ':id']);

        $this->add('editItem')
            ->pageUrl('video/edit/:id')
            ->apiUrl('core/mobile/form/video.video.update/:id');

        $this->add('approveItem')
            ->apiUrl('video/approve/:id')
            ->asPatch();

        $this->add('sponsorItem')
            ->apiUrl('video/sponsor/:id');

        $this->add('sponsorItemInFeed')
            ->apiUrl('video/sponsor-in-feed/:id');

        $this->add('featureItem')
            ->apiUrl('video/feature/:id');

        $this->add('viewMyVideos')
            ->apiUrl('video')
            ->apiParams([
                'view' => 'my',
            ]);
        $this->add('viewPendingVideos')
            ->apiUrl('video')
            ->apiParams([
                'view' => 'pending',
            ]);
        $this->add('viewFriendVideos')
            ->apiUrl('video')
            ->apiParams([
                'view' => 'friend',
            ]);

        $this->add('viewMyPendingVideos')
            ->apiUrl('video')
            ->apiParams([
                'view' => 'my_pending',
            ]);

        $this->add('searchGlobalVideo')
            ->apiUrl(apiUrl('search.index'))
            ->apiParams([
                'view'                        => 'video',
                'q'                           => ':q',
                'owner_id'                    => ':owner_id',
                'when'                        => ':when',
                'related_comment_friend_only' => ':related_comment_friend_only',
                'is_hashtag'                  => ':is_hashtag',
                'from'                        => ':from',
            ]);
    }
}
