<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Group\Http\Resources\v1\Group;

use MetaFox\Group\Models\Group;
use MetaFox\Group\Support\Browse\Scopes\Group\ViewScope;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Resource\WebSetting as Setting;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\SortScope;

/**
 *--------------------------------------------------------------------------
 * Group Web Resource Setting
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
        $this->add('homePage')
            ->pageUrl('group');

        $this->add('joinGroup')
            ->apiUrl('group-member')
            ->asPost();

        $this->add('unjoinGroup')
            ->apiUrl('group-member/:id')
            ->asDelete()
            ->apiParams(['reassign_owner_id' => ':reassign_owner_id'])
            ->confirm([
                'title'   => __p('core::phrase.confirm'),
                'message' => __p('group::phrase.un_join_confirm'),
            ]);

        $this->add('searchItem')
            ->pageUrl('group/search')
            ->pageParams(['view' => Browse::VIEW_SEARCH])
            ->placeholder(__p('group::phrase.search_groups'));

        $this->add('updateAvatar')
            ->apiUrl('group/avatar/:id');

        $this->add('updateProfileCover')
            ->apiUrl('group/cover/:id')
            ->asPost();

        $this->add('removeProfileCover')
            ->apiUrl('group/cover/:id')
            ->asDelete()
            ->confirm(['message' => __p('photo::phrase.delete_confirm')]);

        $this->add('viewAll')
            ->apiUrl('group')
            ->apiRules([
                'q' => ['truthy', 'q'], 'sort' => [
                    'includes', 'sort', ['latest', 'most_viewed', 'most_member', 'most_discussed'],
                ], 'category_id' => ['truthy', 'category_id'], 'type_id' => ['truthy', 'type_id'],
                'when' => ['includes', 'when', ['this_month', 'this_week', 'today']],
                'view' => [
                    'includes', 'view', [
                        Browse::VIEW_MY,
                        Browse::VIEW_FRIEND,
                        Browse::VIEW_PENDING,
                        ViewScope::VIEW_JOINED,
                        ViewScope::VIEW_INVITED,
                        Browse::VIEW_SEARCH,
                        Browse::VIEW_MY_PENDING,
                    ],
                ],
            ]);

        $this->add('viewSearchInGroup')
            ->apiUrl('search')
            ->apiParams([
                'owner_id'                    => ':id',
                'q'                           => ':q',
                'view'                        => ':item_type',
                'when'                        => ':when',
                'sort'                        => ':sort',
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
                'sort' => [
                    'includes',
                    'sort',
                    [
                        Browse::SORT_RECENT,
                        Browse::SORT_MOST_LIKED,
                        Browse::SORT_MOST_VIEWED,
                        Browse::SORT_MOST_DISCUSSED,
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

        $this->add('viewSearchSectionsInGroup')
            ->apiUrl('search/group')
            ->apiParams([
                'owner_id'                    => ':id',
                'q'                           => ':q',
                'when'                        => ':when',
                'sort'                        => ':sort',
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
                'sort' => [
                    'includes',
                    'sort',
                    [
                        Browse::SORT_RECENT,
                        Browse::SORT_MOST_LIKED,
                        Browse::SORT_MOST_VIEWED,
                        Browse::SORT_MOST_DISCUSSED,
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

        $this->add('viewItem')
            ->apiUrl('group/:id')
            ->pageUrl('group/:id')
            ->apiParams(['invite_code' => ':invite_code']);

        $this->add('deleteItem')
            ->apiUrl('group/:id')
            ->confirm(
                [
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('group::phrase.delete_confirm'),
                ]
            );

        $this->add('addItem')
            ->pageUrl('group/add')
            ->apiUrl('group/form');

        $this->add('editItem')
            ->pageUrl('group/settings/:id')
            ->apiUrl('')
            ->asGet();

        $this->add('approveItem')
            ->apiUrl('group/approve/:id')
            ->asPatch();

        $this->add('getGroupInfoForm')
            ->apiUrl('group-info/form/:id')
            ->asGet();

        $this->add('getGroupAboutForm')
            ->apiUrl('group-about/form/:id')
            ->asGet();

        $this->add('sponsorItem')
            ->apiUrl('group/sponsor/:id')
            ->asPatch();

        $this->add('featureItem')
            ->apiUrl('group/feature/:id')
            ->asPatch();

        $this->add('suggestItem')
            ->apiUrl('group/suggestion');

        $this->add('getGroupPermission')
            ->apiUrl('group/privacy/:id')
            ->asGet();

        $this->add('updateGroupPermission')
            ->apiUrl('group/privacy/:id')
            ->asPut();

        $this->add('acceptInvite')
            ->apiUrl('group-invite')
            ->asPut()
            ->apiParams(['group_id' => ':id', 'invite_code' => ':invite_code', 'accept' => 1]);

        $this->add('declineInvite')
            ->apiUrl('group-invite')
            ->asPut()
            ->apiParams(['group_id' => ':id', 'invite_code' => ':invite_code', 'accept' => 0]);

        $this->add('updatePendingMode')
            ->apiUrl('group/pending-mode/:id')
            ->asPatch()
            ->apiParams(['pending_mode' => ':pending_mode']);

        $this->add('viewPendingPost')
            ->apiUrl('feed')
            ->asGet()
            ->apiParams(['user_id' => ':id', 'status' => MetaFoxConstant::ITEM_STATUS_PENDING]);

        $this->add('viewCreatorDeclinedPost')
            ->apiUrl('feed')
            ->asGet()
            ->apiParams([
                'user_id' => ':id',
                'status'  => MetaFoxConstant::ITEM_STATUS_DENIED,
                'view'    => Browse::VIEW_YOUR_CONTENT,
            ])
            ->apiRules(['sort_type' => ['includes', 'sort_type', SortScope::getAllowSortType()]]);

        $this->add('viewCreatorPendingPost')
            ->apiUrl('feed')
            ->asGet()
            ->apiParams([
                'user_id' => ':id',
                'status'  => MetaFoxConstant::ITEM_STATUS_PENDING,
                'view'    => Browse::VIEW_YOUR_CONTENT,
            ])
            ->apiRules(['sort_type' => ['includes', 'sort_type', SortScope::getAllowSortType()]]);

        $this->add('viewCreatorPublishedPost')
            ->apiUrl('feed')
            ->asGet()
            ->apiParams([
                'user_id' => ':id',
                'status'  => MetaFoxConstant::ITEM_STATUS_APPROVED,
                'view'    => Browse::VIEW_YOUR_CONTENT,
            ])
            ->apiRules(['sort_type' => ['includes', 'sort_type', SortScope::getAllowSortType()]]);

        $this->add('viewCreatorRemovedPost')
            ->apiUrl('feed')
            ->asGet()
            ->apiParams([
                'user_id' => ':id',
                'status'  => MetaFoxConstant::ITEM_STATUS_REMOVED,
                'view'    => Browse::VIEW_YOUR_CONTENT,
            ]);

        $this->add('viewModerationRight')
            ->apiUrl('core/form/group.moderation_right/:id')
            ->asGet();

        $this->add('updateModerationRight')
            ->apiUrl('group/moderation-right/:id')
            ->asPut();

        $this->add('inviteFriends')
            ->apiUrl('friend/invite-to-owner')
            ->asGet()
            ->apiParams(['q' => ':q', 'owner_id' => ':id', 'privacy_type' => Group::GROUP_MEMBERS]);

        $this->add('updateRuleConfirmation')
            ->apiUrl('group/confirm-rule')
            ->asPut()
            ->apiParams(['group_id' => ':id', 'is_rule_confirmation' => ':is_rule_confirmation']);

        $this->add('getShareOnGroupSuggestion')
            ->apiUrl('group')
            ->apiParams(['view' => ViewScope::VIEW_JOINED, 'limit' => 10]);

        $this->add('updateAnswerMembershipQuestion')
            ->apiUrl('group/confirm-answer-question')
            ->asPut()
            ->apiParams(['group_id' => ':id', 'is_answer_membership_question' => ':is_answer_membership_question']);

        $this->add('generateInviteLink')
            ->apiUrl('invite-code')
            ->asPost()
            ->apiParams(['group_id' => ':id', 'refresh' => 0]);

        $this->add('groupInfo')
            ->apiUrl('group-info/:id')
            ->pageUrl('group-info/:id')
            ->apiParams(['invite_code' => ':invite_code']);

        $this->add('cancelChangePrivacy')
            ->asPut()
            ->apiUrl('group/privacy/change-request/:id')
            ->confirm([
                'title'        => __p('core::phrase.confirm'),
                'message'      => 'cancel_group_privacy_change_confirmation',
                'phraseParams' => [
                    'oldPrivacyType'     => ':changedPrivacy.current_type',
                    'changedPrivacyType' => ':changedPrivacy.pending_type',
                ],
            ]);

        $this->add('viewPendingGroups')
            ->apiUrl('group')
            ->apiParams([
                'view' => Browse::VIEW_PENDING,
            ]);

        $this->add('getForMentionMembers')
            ->asGet()
            ->apiUrl('friend/mention')
            ->apiParams([
                'q'              => ':q',
                'owner_id'       => ':owner_id',
                'is_member_only' => 1,
            ]);

        $this->add('follow')
            ->apiUrl('follow')
            ->asPost()
            ->apiParams([
                'user_id' => ':id',
            ]);

        $this->add('unfollow')
            ->apiUrl('follow/:id')
            ->asDelete();

        $this->add('shareOnGroupProfile');
    }
}
