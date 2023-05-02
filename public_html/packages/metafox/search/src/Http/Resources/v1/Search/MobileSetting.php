<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Search\Http\Resources\v1\Search;

use MetaFox\Platform\Resource\MobileSetting as Setting;

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
class MobileSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('searchGlobalGroup')
            ->apiUrl(apiUrl('search.group.index'))
            ->apiParams([
                'q'          => ':q',
                'is_hashtag' => ':is_hashtag',
                'from'       => ':from',
            ]);

        $this->add('viewSuggestions')
            ->apiUrl('search/suggestion')
            ->asGet()
            ->apiParams([
                'q'     => ':q',
                'limit' => 10,
            ]);

        $this->add('hashtagTrending')
            ->apiUrl('search/hashtag/trending')
            ->asGet();
    }
}
