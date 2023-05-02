<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Event\Http\Resources\v1\Event;

use MetaFox\Event\Models\Event;
use MetaFox\Event\Models\Member;
use MetaFox\Event\Support\Browse\Scopes\Event\SortScope;
use MetaFox\Event\Support\Browse\Scopes\Event\ViewScope;
use MetaFox\Event\Support\Browse\Scopes\Event\WhenScope;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Resource\MobileSetting as Setting;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\SortScope as ScopesSortScope;

/**
 *--------------------------------------------------------------------------
 * Event Web Resource Setting
 *--------------------------------------------------------------------------
 * stub: /packages/resources/resource_setting.stub
 * Add this class name to resources config gateway.
 */

/**
 * Class WebSetting.
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class MobileSetting extends Setting
{
    protected function initialize(): void
    {
        $this->add('homePage')
            ->pageUrl('event');

        $this->add('searchItem')
            ->apiUrl('event')
            ->apiParams([
                'q'           => ':q',
                'sort'        => ':sort',
                'when'        => ':when',
                'where'       => ':where',
                'view'        => 'search',
                'category_id' => ':category_id',
                'is_online'   => ':is_online',
            ])
            ->placeholder(__p('event::phrase.search_events'));

        $this->add('viewAll')
            ->apiUrl('event')
            ->apiRules([
                'q'     => ['truthy', 'q'],
                'where' => [
                    'truthy',
                    'where',
                ],
                'sort' => [
                    'includes',
                    'sort',
                    [
                        Browse::SORT_RECENT,
                        Browse::SORT_MOST_LIKED,
                        Browse::SORT_MOST_DISCUSSED,
                        SortScope::SORT_MOST_INTERESTED,
                        SortScope::SORT_MOST_MEMBER,
                    ],
                ],
                'category_id' => [
                    'truthy',
                    'category_id',
                ],
                'when' => [
                    'includes',
                    'when',
                    [
                        Browse::WHEN_ALL,
                        Browse::WHEN_THIS_MONTH,
                        Browse::WHEN_THIS_WEEK,
                        Browse::WHEN_TODAY,
                        WhenScope::WHEN_UPCOMING,
                        WhenScope::WHEN_ONGOING,
                        WhenScope::WHEN_PAST,
                    ],
                ],
                'view' => [
                    'includes',
                    'view',
                    [
                        Browse::VIEW_FEATURE,
                        Browse::VIEW_SPONSOR,
                        Browse::VIEW_MY,
                        Browse::VIEW_FRIEND,
                        Browse::VIEW_PENDING,
                        ViewScope::VIEW_GOING,
                        ViewScope::VIEW_INTERESTED,
                        ViewScope::VIEW_INVITES,
                    ],
                ],
                'is_online' => ['truthy', 'is_online'],
            ]);

        $this->add('viewFriendEvents')
            ->apiUrl('event')
            ->apiParams([
                'view' => Browse::VIEW_FRIEND,
            ]);

        $this->add('viewInvites')
            ->apiUrl('event')
            ->apiParams([
                'view' => 'invites',
            ]);
        $this->add('viewPendingEvents')
            ->apiUrl('event')
            ->apiParams([
                'view' => Browse::VIEW_PENDING,
            ]);

        $this->add('viewSimilar')
            ->apiUrl('event')
            ->apiParams([
                'event_id' => ':id',
                'view'     => Browse::VIEW_SIMILAR,
                'sort'     => SortScope::SORT_RANDOM,
                'limit'    => 1,
            ]);

        $this->add('viewInterested')
            ->apiUrl('event')
            ->apiParams([
                'sort' => SortScope::SORT_DEFAULT,
                'view' => ViewScope::VIEW_INTERESTED,
            ]);

        $this->add('viewHosting')
            ->apiUrl('event')
            ->apiParams([
                'sort' => SortScope::SORT_DEFAULT,
                'view' => ViewScope::VIEW_HOSTING,
            ]);

        $this->add('viewGoing')
            ->apiUrl('event')
            ->apiParams([
                'sort' => SortScope::SORT_DEFAULT,
                'view' => ViewScope::VIEW_GOING,
            ]);

        $this->add('viewPast')
            ->apiUrl('event')
            ->apiParams([
                'sort' => SortScope::SORT_DEFAULT,
                'when' => WhenScope::WHEN_PAST,
            ]);

        $this->add('viewRelatedPast')
            ->apiUrl('event')
            ->apiParams([
                'sort' => SortScope::SORT_END_TIME,
                'view' => ViewScope::VIEW_RELATED,
                'when' => WhenScope::WHEN_PAST,
            ]);

        $this->add('viewUpcoming')
            ->apiUrl('event')
            ->apiParams([
                'sort' => SortScope::SORT_DEFAULT,
                'when' => WhenScope::WHEN_UPCOMING,
            ]);

        $this->add('viewItem')
            ->apiUrl('event/:id')
            ->apiParams([
                'invite_code' => ':invite_code',
            ]);

        $this->add('deleteItem')
            ->apiUrl('event/:id')
            ->asDelete()
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('event::phrase.confirm_delete'),
                ]
            );

        $this->add('addItem')
            ->apiUrl('core/mobile/form/event.event.store')
            ->apiParams(['owner_id' => ':id']);

        $this->add('editItem')
            ->apiUrl('core/mobile/form/event.event.update/:id');

        $this->add('sponsorItem')
            ->apiUrl('event/sponsor/:id')
            ->asPatch();

        $this->add('featureItem')
            ->apiUrl('event/feature/:id')
            ->asPatch();

        $this->add('invitePeopleToCome')
            ->apiUrl('event-invite')
            ->asPost()
            ->apiParams([
                'event_id' => ':id',
                'users'    => ':ids',
            ]);

        $this->add('inviteHosts')
            ->apiUrl('event-host-invite')
            ->asPost()
            ->apiParams([
                'event_id' => ':id',
                'user_ids' => ':ids',
            ]);

        $this->add('suggestFriends')
            ->apiUrl('friend/invite-to-owner')
            ->asGet()
            ->apiParams([
                'q'            => ':q',
                'owner_id'     => ':id',
                'privacy_type' => Event::EVENT_MEMBERS,
            ]);

        $this->add('joinEvent')
            ->apiUrl('event-member')
            ->asPost()
            ->apiParams([
                'event_id' => ':id',
            ]);

        $this->add('leaveEvent')
            ->apiUrl('event-member/:id')
            ->asDelete()
            ->confirm([
                'title'   => 'Confirm',
                'message' => 'Are you sure you want to leave this event?',
            ]);

        $this->add('interestedEvent')
            ->apiUrl('event-member/interest/:id')
            ->asPut()
            ->apiParams(['interest' => Member::INTERESTED]);

        $this->add('notInterestedEvent')
            ->apiUrl('event-member/interest/:id')
            ->asPut()
            ->apiParams(['interest' => Member::NOT_INTERESTED]);

        $this->add('settingForm')
            ->apiUrl('event/setting/form/:id')
            ->asGet()
            ->apiParams(['event_id' => ':id']);

        $this->add('viewPendingPost')
            ->apiUrl('feed')
            ->asGet()
            ->apiParams(['user_id' => ':id', 'status' => MetaFoxConstant::ITEM_STATUS_PENDING])
            ->apiRules(['sort_type' => ['includes', 'sort_type', ScopesSortScope::getAllowSortType()]]);
        $this->add('viewMyPendingPost')
            ->apiUrl('feed')
            ->asGet()
            ->apiParams(
                [
                    'user_id' => ':id',
                    'view'    => 'your_content',
                    'status'  => MetaFoxConstant::ITEM_STATUS_PENDING,
                ]
            )
            ->apiRules(['sort_type' => ['includes', 'sort_type', ScopesSortScope::getAllowSortType()]]);

        $this->add('viewCreatorPendingPost')
            ->apiUrl('feed')
            ->asGet()
            ->apiParams([
                'user_id' => ':id', 'status' => MetaFoxConstant::ITEM_STATUS_PENDING,
                'view'    => Browse::VIEW_YOUR_CONTENT,
            ])
            ->apiRules(['sort_type' => ['includes', 'sort_type', ScopesSortScope::getAllowSortType()]]);

        $this->add('approveItem')
            ->apiUrl('event/approve/:id')
            ->asPatch();

        $this->add('updatePrivacySettings')
            ->apiUrl('core/mobile/form/event.setting/:id')
            ->asGet();

        $this->add('searchGlobalEvent')
            ->apiUrl(apiUrl('search.index'))
            ->apiParams([
                'view'                        => 'event',
                'q'                           => ':q',
                'owner_id'                    => ':owner_id',
                'when'                        => ':when',
                'related_comment_friend_only' => ':related_comment_friend_only',
                'is_hashtag'                  => ':is_hashtag',
                'from'                        => ':from',
            ]);

        $this->add('viewMyPendingEvents')
            ->apiUrl('event')
            ->apiParams([
                'view' => Browse::VIEW_MY_PENDING,
            ]);

        $this->add('massEmailEvent')
            ->apiUrl('core/mobile/form/event.mass_email/:id')
            ->asGet();
    }
}
