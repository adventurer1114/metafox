<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Search\Http\Resources\v1\Search;

use MetaFox\Platform\Resource\WebSetting as Setting;
use MetaFox\Platform\Support\Helper\Pagination;

/**
 *--------------------------------------------------------------------------
 * Search Web Resource Setting
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
        $this->add('viewSections')
            ->apiUrl('search/group')
            ->apiParams([
                'q'                           => ':q',
                'is_hashtag'                  => ':is_hashtag',
                'from'                        => ':from',
                'related_comment_friend_only' => ':related_comment_friend_only',
            ])
            ->asGet()
            ->apiRules([
                'q' => [
                    'truthy',
                    'q',
                ],
                'is_hashtag' => [
                    'truthy',
                    'is_hashtag',
                ],
                'from' => [
                    'truthy',
                    'from',
                ],
                'related_comment_friend_only' => [
                    'truthy',
                    'related_comment_friend_only',
                ],
            ]);

        $this->add('viewAll')
            ->apiUrl('search')
            ->apiParams([
                'q'                           => ':q',
                'limit'                       => Pagination::DEFAULT_ITEM_PER_PAGE,
                'page'                        => ':page',
                'last_search_id'              => ':last_search_id',
                'view'                        => ':item_type',
                'is_hashtag'                  => ':is_hashtag',
                'from'                        => ':from',
                'related_comment_friend_only' => ':related_comment_friend_only',
            ])
            ->apiRules([
                'q'                           => ['truthy', 'q'],
                'limit'                       => ['truthy', 'limit'],
                'page'                        => ['truthy', 'limit'],
                'last_search_id'              => ['truthy', 'limit'],
                'view'                        => ['truthy', 'view'],
                'is_hashtag'                  => ['truthy', 'is_hashtag'],
                'from'                        => ['truthy', 'from'],
                'related_comment_friend_only' => ['truthy', 'related_comment_friend_only'],
            ]);

        $this->add('viewSuggestions')
            ->apiUrl('search/suggestion')
            ->asGet()
            ->apiRules([
                'q'     => ['truthy', 'q'],
                'limit' => ['truthy', 'limit'],
            ]);

        $this->add('hashtagTrending')
            ->apiUrl('search/hashtag/trending')
            ->asGet();
    }
}
