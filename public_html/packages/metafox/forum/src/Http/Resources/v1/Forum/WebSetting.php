<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Forum\Http\Resources\v1\Forum;

use MetaFox\Forum\Support\Browse\Scopes\ThreadSortScope;
use MetaFox\Forum\Support\Browse\Scopes\ThreadViewScope;
use MetaFox\Forum\Support\Facades\Forum as ForumFacade;
use MetaFox\Forum\Support\ForumSupport;
use MetaFox\Platform\Resource\WebSetting as ResourceSetting;
use MetaFox\Platform\Support\Browse\Scopes\SortScope;
use MetaFox\Platform\Support\Browse\Scopes\WhenScope;

/**
 *--------------------------------------------------------------------------
 * Forum Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class WebSetting.
 */
class WebSetting extends ResourceSetting
{
    protected function initialize(): void
    {
        $this->add('searchItem')
            ->pageUrl('forum/search')
            ->placeholder(__p('forum::web.search_discussions'));

        $this->add('viewAll')
            ->apiUrl('forum')
            ->apiRules([
                'q'         => ['truthy', 'q'],
                'view'      => ['includes', 'view', ThreadViewScope::getAllowView()],
                'sort'      => ['includes', 'sort', ThreadSortScope::getAllowSort()],
                'sort_type' => ['includes', 'sort_type', SortScope::getAllowSortType()],
                'when'      => ['includes', 'when', WhenScope::getAllowWhen()],
                'item_type' => ['includes', 'item_type', ForumFacade::getItemTypesForSearch()],
                'forum_id'  => ['truthy', 'forum_id'],
            ]);

        $this->add('homePage')
            ->pageUrl('forum');

        $this->add('viewItem')
            ->pageUrl('forum/:id')
            ->apiUrl('forum/:id');

        $this->add('viewSubForum')
            ->apiUrl('forum-subs/:id');

        $this->add('viewQuickNavigationItems')
            ->apiUrl('forum')
            ->apiParams([
                'view' => ForumSupport::VIEW_QUICK_NAVIGATION,
            ]);
    }
}
