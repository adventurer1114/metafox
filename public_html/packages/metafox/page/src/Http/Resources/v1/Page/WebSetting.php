<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Page\Http\Resources\v1\Page;

use MetaFox\Page\Models\Page;
use MetaFox\Page\Support\Browse\Scopes\Page\ViewScope;
use MetaFox\Platform\Resource\WebSetting as Setting;
use MetaFox\Platform\Support\Browse\Browse;

/**
 * class WebSetting.
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class WebSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('homePage')
            ->pageUrl('page');

        $this->add('searchItem')
            ->pageUrl('page/search')
            ->placeholder(__p('page::phrase.search_pages'));

        $this->add('updateProfileCover')
            ->apiUrl('page/cover/:id')
            ->asPost();

        $this->add('removeProfileCover')
            ->apiUrl('page/cover/:id')
            ->asDelete()
            ->confirm(['message' => 'Are you sure you want to delete this photo?']);

        $this->add('updateAvatar')
            ->apiUrl('page/avatar/:id');

        $this->add('viewAll')
            ->apiUrl('page')
            ->apiRules([
                'q'       => ['truthy', 'q'],
                'sort'    => ['includes', 'sort', ['recent', 'most_viewed', 'most_member', 'most_discussed']],
                'type_id' => ['truthy', 'type_id'], 'category_id' => ['truthy', 'category_id'],
                'when'    => ['includes', 'when', ['all', 'this_month', 'this_week', 'today']],
                'view'    => [
                    'includes', 'view',
                    [
                        Browse::VIEW_MY,
                        Browse::VIEW_FRIEND,
                        Browse::VIEW_PENDING,
                        Browse::VIEW_MY_PENDING,
                        Browse::VIEW_SEARCH,
                        ViewScope::VIEW_INVITED,
                        ViewScope::VIEW_LIKED,
                    ],
                ],
            ]);

        $this->add('viewSearchInPage')
            ->apiUrl('feed/?user_id=:id')
            ->apiRules([
                'q'    => ['truthy', 'q'],
                'sort' => [
                    'includes', 'sort', ['recent', 'most_viewed', 'most_member', 'most_discussed'],
                ],
                'when'                        => ['includes', 'when', ['this_month', 'this_week', 'today']],
                'view'                        => ['includes', 'view', ['my', 'friend', 'invited', 'joined']],
                'related_comment_friend_only' => [
                    'or', ['truthy', 'related_comment_friend_only'], ['falsy', 'related_comment_friend_only'],
                ],
            ]);

        $this->add('viewItem')
            ->apiUrl('page/:id')
            ->pageUrl('page/:id');

        $this->add('deleteItem')
            ->apiUrl('page/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('page::phrase.delete_confirm'),
                ]
            );

        $this->add('addItem')
            ->pageUrl('page/add')
            ->apiUrl('page/form');

        $this->add('editItem')
            ->pageUrl('page/settings/:id')
            ->apiUrl('');

        $this->add('sponsorItem')
            ->apiUrl('page/sponsor/:id');

        $this->add('featureItem')
            ->apiUrl('page/feature/:id');

        $this->add('suggestItem')
            ->apiUrl('page/suggestion');

        $this->add('getPageInfoForm')
            ->apiUrl('page-info/form/:id')
            ->asGet();

        $this->add('getPageAboutForm')
            ->apiUrl('page-about/form/:id')
            ->asGet();

        $this->add('getPagePermission')
            ->apiUrl('page/privacy/:id')
            ->asGet();

        $this->add('updatePagePermission')
            ->apiUrl('page/privacy/:id')
            ->asPut();

        $this->add('inviteFriends')
            ->apiUrl('friend/invite-to-owner')
            ->asGet()
            ->apiParams(['q' => ':q', 'owner_id' => ':id', 'privacy_type' => Page::PAGE_MEMBERS]);

        $this->add('getShareOnPageSuggestion')
            ->apiUrl('page')
            ->apiParams(['view' => ViewScope::VIEW_LIKED, 'limit' => 10]);

        $this->add('viewSimilar')
            ->apiUrl('page/similar')
            ->apiParams([
                'page_id' => ':id',
                'limit'   => 1,
            ]);

        $this->add('approveItem')
            ->apiUrl('page/approve/:id')
            ->asPatch();

        $this->add('viewSearchInPage')
            ->apiUrl('search')
            ->apiParams([
                'owner_id'                    => ':id',
                'q'                           => ':q',
                'view'                        => ':item_type',
                'when'                        => ':when',
                'related_comment_friend_only' => ':related_comment_friend_only',
            ])
            ->apiRules([
                'q'    => ['truthy', 'q'],
                'view' => ['truthy', 'view'],
                'when' => [
                    'includes',
                    'when',
                    [
                        'this_month',
                        'this_week',
                        'today',
                        'all',
                    ],
                ],
                'related_comment_friend_only' => [
                    'or',
                    [
                        'truthy',
                        'related_comment_friend_only',
                    ],
                    [
                        'falsy',
                        'related_comment_friend_only',
                    ],
                ],
                'owner_id' => ['truthy', 'owner_id'],
            ]);

        $this->add('viewSearchSectionsInPage')
            ->apiUrl('search/group')
            ->apiParams([
                'owner_id'                    => ':id',
                'q'                           => ':q',
                'when'                        => ':when',
                'related_comment_friend_only' => ':related_comment_friend_only',
            ])
            ->apiRules([
                'q'        => ['truthy', 'q'],
                'owner_id' => ['truthy', 'owner_id'],
                'when'     => [
                    'includes',
                    'when',
                    [
                        'this_month',
                        'this_week',
                        'today',
                        'all',
                    ],
                ],
                'related_comment_friend_only' => [
                    'or',
                    [
                        'truthy',
                        'related_comment_friend_only',
                    ],
                    [
                        'falsy',
                        'related_comment_friend_only',
                    ],
                ],
            ]);

        $this->add('claimDialog')
            ->apiUrl('core/form/page.page.claim/:id');

        $this->add('follow')
            ->apiUrl('follow')
            ->asPost()
            ->apiParams([
                'user_id' => ':id',
            ]);

        $this->add('unfollow')
            ->apiUrl('follow/:id')
            ->asDelete();

        $this->add('shareOnPageProfile');
    }
}
