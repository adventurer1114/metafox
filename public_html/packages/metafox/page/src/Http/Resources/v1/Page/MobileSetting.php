<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use MetaFox\Page\Models\Page;
use MetaFox\Page\Support\Browse\Scopes\Page\ViewScope;
use MetaFox\Platform\Resource\MobileSetting as Setting;

/**
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class MobileSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('searchItem')
            ->apiUrl('page')
            ->apiParams([
                'q'           => ':q',
                'sort'        => ':sort',
                'when'        => ':when',
                'category_id' => ':category_id',
                'view'        => 'search',
            ])
            ->placeholder(__p('page::phrase.search_pages'));

        $this->add('reassignOwnerForm')
            ->apiUrl('core/mobile/form/page.page_member.reassign_owner/:id')
            ->asGet();

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
                'q'           => ['truthy', 'q'],
                'sort'        => ['includes', 'sort', ['recent', 'most_viewed', 'most_member', 'most_discussed']],
                'type_id'     => ['truthy', 'type_id'],
                'category_id' => ['truthy', 'category_id'],
                'when'        => ['includes', 'when', ['all', 'this_month', 'this_week', 'today']],
                'view'        => ['includes', 'view', ['my', 'friend', 'pending', 'invited', 'liked']],
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
            ->pageUrl('pages/:id');

        $this->add('deleteItem')
            ->apiUrl('page/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('page::phrase.delete_confirm'),
                ]
            );

        $this->add('addItem')
            ->pageUrl('pages/add')
            ->apiUrl('core/mobile/form/page.page.store');

        $this->add('editItem')
            ->pageUrl('pages/settings/:id')
            ->apiUrl('');

        $this->add('sponsorItem')
            ->apiUrl('page/sponsor/:id');

        $this->add('featureItem')
            ->apiUrl('page/feature/:id');

        $this->add('suggestItem')
            ->apiUrl('page/suggestion');

        $this->add('getPageInfoForm')
            ->apiUrl('core/mobile/form/page.page.info/:id')
            ->asGet();

        $this->add('getPageAboutForm')
            ->apiUrl('core/mobile/form/page.page.about/:id')
            ->asGet();

        $this->add('getPagePermission')
            ->apiUrl('page/privacy/:id')
            ->asGet();

        $this->add('getPagePermissionForm')
            ->apiUrl('core/mobile/form/page.page.permission/:id')
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

        $this->add('viewMyPages')
            ->apiUrl('page')
            ->apiParams(['view' => 'my']);

        $this->add('viewFriendPages')
            ->apiUrl('page')
            ->apiParams(['view' => 'friend']);

        $this->add('viewLikedPages')
            ->apiUrl('page')
            ->apiParams(['view' => 'liked']);

        $this->add('viewInvitedPages')
            ->apiUrl('page')
            ->apiParams(['view' => 'invited']);

        $this->add('viewPendingPages')
            ->apiUrl('page')
            ->apiParams(['view' => 'pending']);

        $this->add('searchGlobalPage')
            ->apiUrl(apiUrl('search.index'))
            ->apiParams([
                'view'       => 'page',
                'q'          => ':q',
                'is_hashtag' => ':is_hashtag',
            ]);

        $this->add('viewMyPendingPages')
            ->apiUrl('page')
            ->apiParams([
                'view' => 'my_pending',
            ]);
        $this->add('approveItem')
            ->apiUrl('page/approve/:id')
            ->asPatch();

        $this->add('searchGlobalInPage')
            ->apiUrl(apiUrl('search.group.index'))
            ->apiParams([
                'q'                           => ':q',
                'owner_id'                    => ':owner_id',
                'when'                        => ':when',
                'related_comment_friend_only' => ':related_comment_friend_only',
            ]);

        $this->add('claimDialog')
            ->apiUrl('core/mobile/form/page.page.claim/:id');

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
