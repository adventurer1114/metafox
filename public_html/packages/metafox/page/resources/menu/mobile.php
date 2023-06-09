<?php

/* this is auto generated file */
return [
    [
        'menu'     => 'page.page.filter_menu',
        'name'     => 'all',
        'label'    => 'core::phrase.when.all',
        'ordering' => 1,
        'value'    => 'all',
    ],
    [
        'menu'     => 'page.page.filter_menu',
        'name'     => 'this_month',
        'label'    => 'core::phrase.when.this_month',
        'ordering' => 2,
        'value'    => 'this_month',
    ],
    [
        'menu'     => 'page.page.filter_menu',
        'name'     => 'this_week',
        'label'    => 'core::phrase.when.this_week',
        'ordering' => 3,
        'value'    => 'this_week',
    ],
    [
        'menu'     => 'page.page.filter_menu',
        'name'     => 'today',
        'label'    => 'core::phrase.when.today',
        'ordering' => 4,
        'value'    => 'today',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'page.page.itemActionMenu',
        'name'     => 'invite',
        'label'    => 'page::phrase.invite_friends',
        'ordering' => 2,
        'value'    => 'page/inviteFriends',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_pending'],
            ['truthy', 'item.extra.can_approve'],
        ],
        'menu'     => 'page.page.itemActionMenu',
        'name'     => 'approve',
        'label'    => 'group::phrase.approve',
        'ordering' => 3,
        'value'    => 'approveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['falsy', 'item.is_sponsored_feed'],
        ],
        'menu'     => 'page.page.itemActionMenu',
        'name'     => 'sponsor_in_feed',
        'label'    => 'page::phrase.sponsor_in_feed',
        'ordering' => 3,
        'value'    => 'sponsorItemInFeed',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['truthy', 'item.is_sponsored_feed'],
        ],
        'menu'     => 'page.page.itemActionMenu',
        'name'     => 'unsponsor_in_feed',
        'label'    => 'page::phrase.unsponsor_in_feed',
        'ordering' => 4,
        'value'    => 'unsponsorItemInFeed',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['falsy', 'item.is_sponsor'],
        ],
        'menu'     => 'page.page.itemActionMenu',
        'name'     => 'sponsor',
        'label'    => 'page::phrase.sponsor_this_item',
        'ordering' => 5,
        'value'    => 'sponsorItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['truthy', 'item.is_sponsor'],
        ],
        'menu'     => 'page.page.itemActionMenu',
        'name'     => 'unsponsor',
        'label'    => 'page::phrase.unsponsor_this_item',
        'ordering' => 6,
        'value'    => 'unsponsorItem',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
        ],
        'menu'     => 'page.page.itemActionMenu',
        'name'     => 'feature',
        'label'    => 'core::phrase.feature',
        'ordering' => 7,
        'value'    => 'featureItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
        ],
        'menu'     => 'page.page.itemActionMenu',
        'name'     => 'unfeature',
        'label'    => 'core::phrase.un_feature',
        'ordering' => 8,
        'value'    => 'unfeatureItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_delete'],
        ],
        'className' => 'itemDelete',
        'style'     => 'danger',
        'menu'      => 'page.page.itemActionMenu',
        'name'      => 'delete',
        'label'     => 'page::phrase.delete',
        'ordering'  => 10,
        'value'     => 'deleteItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_add_cover'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'update_cover',
        'label'    => 'page::phrase.update_cover',
        'ordering' => 1,
        'value'    => '@app/EDIT_ITEM_COVER',
        'icon'     => '',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'update_avatar',
        'label'    => 'page::phrase.update_avatar',
        'ordering' => 2,
        'value'    => '@app/EDIT_ITEM_AVATAR',
        'icon'     => '',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_remove_cover'],
            ['truthy', 'item.cover_photo_id'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'remove_cover',
        'label'    => 'page::phrase.remove_cover_photo',
        'ordering' => 3,
        'value'    => 'page/remove_cover',
        'icon'     => '',
    ],
    [
        'showWhen' => ['truthy', 'item.extra.can_message'],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'message',
        'label'    => 'page::phrase.message',
        'ordering' => 5,
        'value'    => 'comingSoon',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_claim'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'claim',
        'label'    => 'page::phrase.claim_page',
        'ordering' => 6,
        'value'    => 'page/claim',
    ],
    [
        'showWhen' => ['truthy', 'item.extra.can_edit'],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'manage',
        'label'    => 'page::phrase.manage',
        'ordering' => 7,
        'value'    => 'pages/manage',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'invite',
        'label'    => 'page::phrase.invite_friends',
        'ordering' => 8,
        'value'    => 'page/inviteFriends',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['falsy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'feature',
        'label'    => 'core::phrase.feature',
        'ordering' => 9,
        'value'    => 'featureItem',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['truthy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'unfeature',
        'label'    => 'core::phrase.un_feature',
        'ordering' => 9,
        'value'    => 'unfeatureItem',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_moderate'],
            ['falsy', 'item.is_sponsor'],
            [
                'or',
                ['truthy', 'item.extra.can_sponsor'],
                ['truthy', 'item.extra.can_purchase_sponsor'],
            ],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'sponsor',
        'label'    => 'page::phrase.sponsor',
        'ordering' => 10,
        'value'    => 'sponsorItem',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_moderate'],
            ['truthy', 'item.is_sponsor'],
            [
                'or',
                ['truthy', 'item.extra.can_sponsor'],
                ['truthy', 'item.extra.can_purchase_sponsor'],
            ],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'unsponsor',
        'label'    => 'page::phrase.un_sponsor',
        'ordering' => 11,
        'value'    => 'unsponsorItem',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            [
                'or',
                ['truthy', 'item.is_owner'],
                ['truthy', 'item.extra.can_moderate'],
            ],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'reassign_owner',
        'label'    => 'page::phrase.reassign_owner',
        'ordering' => 12,
        'value'    => 'page/reassignOwnerDialog',
    ],
    [
        'showWhen' => [
            'or',
            ['truthy', 'item.extra.can_delete'],
        ],
        'className' => 'itemDelete',
        'style'     => 'danger',
        'menu'      => 'page.page.profileActionMenu',
        'name'      => 'delete',
        'label'     => 'page::phrase.delete',
        'ordering'  => 15,
        'value'     => 'deleteItem',
    ],
    [
        'active'   => true,
        'tab'      => 'home',
        'menu'     => 'page.page.profileMenu',
        'name'     => 'home',
        'label'    => 'page::phrase.overview',
        'ordering' => 1,
        'to'       => '/',
    ],
    [
        'tab'      => 'about',
        'menu'     => 'page.page.profileMenu',
        'name'     => 'about',
        'label'    => 'page::phrase.about',
        'ordering' => 2,
        'to'       => '/about',
    ],
    [
        'tab'      => 'member',
        'menu'     => 'page.page.profileMenu',
        'name'     => 'members',
        'label'    => 'page::phrase.members',
        'ordering' => 3,
        'to'       => '/member',
    ],
    [
        'menu'     => 'page.page.profilePopoverMenu',
        'name'     => 'message',
        'label'    => 'page::phrase.message',
        'ordering' => 1,
        'value'    => 'comingSoon',
    ],
    [
        'showWhen' => [],
        'menu'     => 'page.page.profilePopoverMenu',
        'name'     => 'invite',
        'label'    => 'page::phrase.invite_friends',
        'ordering' => 2,
        'value'    => 'page/inviteFriends',
    ],
    [
        'menu'     => 'page.page.sort_menu',
        'name'     => 'recent',
        'label'    => 'core::phrase.sort.recent',
        'ordering' => 1,
        'value'    => 'recent',
    ],
    [
        'menu'     => 'page.page.sort_menu',
        'name'     => 'most_member',
        'label'    => 'core::phrase.sort.most_liked',
        'ordering' => 2,
        'value'    => 'most_member',
    ],
    [
        'showWhen' => [
            'and',
            [
                'includes',
                'item.friendship',
                [0, 7],
            ],
            ['truthy', 'item.extra.can_add_friend'],
        ],
        'menu'     => 'page.page_member.itemActionMenu',
        'name'     => 'add_as_friend',
        'label'    => 'page::phrase.add_as_friend',
        'ordering' => 1,
        'value'    => 'user/addFriend',
        'icon'     => '',
    ],
    [
        'showWhen' => ['eq', 'item.friendship', 3],
        'menu'     => 'page.page_member.itemActionMenu',
        'name'     => 'cancel_request',
        'label'    => 'page::phrase.cancel_request',
        'ordering' => 2,
        'value'    => 'user/cancelRequest',
        'icon'     => '',
    ],
    [
        'showWhen' => ['eq', 'item.friendship', 1],
        'style'    => 'danger',
        'menu'     => 'page.page_member.itemActionMenu',
        'name'     => 'unfriend',
        'label'    => 'page::phrase.unfriend',
        'ordering' => 3,
        'value'    => 'user/unFriend',
        'icon'     => '',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.member_type', 1],
            ['truthy', 'item.extra.can_remove_as_admin'],
        ],
        'menu'     => 'page.page_member.itemActionMenu',
        'name'     => 'remove_as_admin',
        'label'    => 'page::phrase.remove_as_admin',
        'ordering' => 4,
        'value'    => 'page/removeAsAdmin',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.member_type', 0],
            ['truthy', 'item.extra.can_set_as_admin'],
            ['falsy', 'item.extra.can_cancel_admin_invite'],
        ],
        'menu'     => 'page.page_member.itemActionMenu',
        'name'     => 'set_as_admin',
        'label'    => 'page::phrase.set_as_admin',
        'ordering' => 5,
        'value'    => 'page/setAsAdmin',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.member_type', 0],
            ['truthy', 'item.extra.can_set_as_admin'],
            ['truthy', 'item.extra.can_cancel_admin_invite'],
        ],
        'menu'     => 'page.page_member.itemActionMenu',
        'name'     => 'remove_admin_invite',
        'label'    => 'page::phrase.remove_admin_invite',
        'ordering' => 6,
        'value'    => 'page/removeAdminInvite',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.member_type', 1],
            ['truthy', 'item.extra.can_reassign_owner'],
        ],
        'menu'     => 'page.page_member.itemActionMenu',
        'name'     => 'reassign_owner',
        'label'    => 'page::phrase.reassign_owner',
        'ordering' => 7,
        'value'    => 'page/reassignOwner',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_block'],
        ],
        'menu'     => 'page.page_member.itemActionMenu',
        'name'     => 'block_from_page',
        'label'    => 'page::phrase.block_from_page',
        'ordering' => 8,
        'value'    => 'page/blockFromPage',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.member_type', 0],
            ['truthy', 'item.extra.can_remove_member'],
        ],
        'menu'     => 'page.page_member.itemActionMenu',
        'name'     => 'remove_member',
        'label'    => 'page::phrase.remove_member',
        'ordering' => 9,
        'value'    => 'page/removeMember',
    ],
    [
        'tab'    => 'all',
        'params' => [
            'module_name'   => 'page',
            'resource_name' => 'page',
        ],
        'menu'     => 'page.sidebarMenu',
        'name'     => 'all',
        'label'    => 'page::phrase.all_pages',
        'ordering' => 1,
        'value'    => 'viewAll',
        'to'       => '/pages/all',
    ],
    [
        'tab'      => 'my',
        'showWhen' => [],
        'params'   => [
            'module_name'   => 'page',
            'resource_name' => 'page',
        ],
        'menu'     => 'page.sidebarMenu',
        'name'     => 'my',
        'label'    => 'page::phrase.my_pages',
        'ordering' => 2,
        'value'    => 'viewMyPages',
        'to'       => '/pages/my',
    ],
    [
        'params' => [
            'module_name'   => 'page',
            'resource_name' => 'page',
        ],
        'showWhen' => [
            'and',
            ['neq', 'session.user.role.id', 1],
        ],
        'menu'     => 'page.sidebarMenu',
        'name'     => 'my_pending',
        'label'    => 'page::phrase.my_pending_pages',
        'ordering' => 3,
        'value'    => 'viewMyPendingPages',
    ],
    [
        'tab'      => 'friend',
        'showWhen' => [],
        'params'   => [
            'module_name'   => 'page',
            'resource_name' => 'page',
        ],
        'menu'     => 'page.sidebarMenu',
        'name'     => 'friend',
        'label'    => 'page::phrase.friends_pages',
        'ordering' => 4,
        'value'    => 'viewFriendPages',
        'to'       => '/pages/friend',
    ],
    [
        'tab'      => 'liked',
        'showWhen' => [],
        'params'   => [
            'module_name'   => 'page',
            'resource_name' => 'page',
        ],
        'menu'     => 'page.sidebarMenu',
        'name'     => 'like',
        'label'    => 'page::phrase.liked_pages',
        'ordering' => 5,
        'value'    => 'viewLikedPages',
        'to'       => '/pages/liked',
    ],
    [
        'tab'      => 'invited',
        'showWhen' => [],
        'params'   => [
            'module_name'   => 'page',
            'resource_name' => 'page',
        ],
        'menu'     => 'page.sidebarMenu',
        'name'     => 'invited',
        'label'    => 'page::phrase.page_invites',
        'ordering' => 6,
        'value'    => 'viewInvitedPages',
        'to'       => '/pages/invited',
    ],
    [
        'tab'      => 'pending',
        'showWhen' => [
            'or',
            ['truthy', 'acl.page.page.approve'],
        ],
        'params' => [
            'module_name'   => 'page',
            'resource_name' => 'page',
        ],
        'menu'     => 'page.sidebarMenu',
        'name'     => 'pending',
        'label'    => 'page::phrase.pending_pages',
        'ordering' => 7,
        'value'    => 'viewPendingPages',
        'to'       => '/pages/pending',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.page.page.view'],
            ['falsy', 'is_hashtag'],
        ],
        'module_name'   => 'page',
        'resource_name' => 'page',
        'menu'          => 'search.mobileCategoryMenu',
        'name'          => 'page',
        'label'         => 'page::phrase.page_global_search_label',
        'ordering'      => 8,
        'value'         => 'searchGlobalPage',
    ],
    [
        'tab'      => 'page',
        'showWhen' => [
            'and',
            ['truthy', 'item.profile_menu_settings.page_profile_menu'],
        ],
        'menu'     => 'user.user.profileMenu',
        'name'     => 'pages',
        'label'    => 'page::phrase.pages',
        'ordering' => 7,
        'to'       => '/page',
    ],
    [
        'module_name'   => 'page',
        'resource_name' => 'page',
        'menu'          => 'page.mobileCategoryMenu',
        'name'          => 'all',
        'label'         => 'core::phrase.all',
        'ordering'      => 0,
        'value'         => 'searchGlobalInPage',
    ],
    [
        'showWhen' => [],
        'menu'     => 'page.page.detailActionMenu',
        'name'     => 'invite',
        'label'    => 'page::phrase.invite_friends',
        'ordering' => 2,
        'value'    => 'page/inviteFriends',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_claim'],
        ],
        'menu'     => 'page.page.detailActionMenu',
        'name'     => 'claim',
        'label'    => 'page::phrase.claim_page',
        'ordering' => 3,
        'value'    => 'page/claim',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['falsy', 'item.is_sponsored_feed'],
        ],
        'menu'     => 'page.page.detailActionMenu',
        'name'     => 'sponsor_in_feed',
        'label'    => 'page::phrase.sponsor_in_feed',
        'ordering' => 4,
        'value'    => 'sponsorItemInFeed',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['truthy', 'item.is_sponsored_feed'],
        ],
        'menu'     => 'page.page.detailActionMenu',
        'name'     => 'unsponsor_in_feed',
        'label'    => 'page::phrase.unsponsor_in_feed',
        'ordering' => 5,
        'value'    => 'unsponsorItemInFeed',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['falsy', 'item.is_sponsor'],
        ],
        'menu'     => 'page.page.detailActionMenu',
        'name'     => 'sponsor',
        'label'    => 'page::phrase.sponsor_this_item',
        'ordering' => 6,
        'value'    => 'sponsorItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['truthy', 'item.is_sponsor'],
        ],
        'menu'     => 'page.page.detailActionMenu',
        'name'     => 'unsponsor',
        'label'    => 'page::phrase.unsponsor_this_item',
        'ordering' => 7,
        'value'    => 'unsponsorItem',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
        ],
        'menu'     => 'page.page.detailActionMenu',
        'name'     => 'feature',
        'label'    => 'core::phrase.feature',
        'ordering' => 8,
        'value'    => 'featureItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
        ],
        'menu'     => 'page.page.detailActionMenu',
        'name'     => 'unfeature',
        'label'    => 'core::phrase.un_feature',
        'ordering' => 8,
        'value'    => 'unfeatureItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_delete'],
        ],
        'className' => 'itemDelete',
        'style'     => 'danger',
        'menu'      => 'page.page.detailActionMenu',
        'name'      => 'delete',
        'label'     => 'page::phrase.delete',
        'ordering'  => 10,
        'value'     => 'deleteItem',
    ],
    [
        'showWhen'  => [],
        'menu'      => 'core.bodyMenu',
        'name'      => 'page',
        'label'     => 'page::phrase.pages',
        'ordering'  => 5,
        'value'     => '',
        'to'        => '/page',
        'as'        => 'item',
        'icon'      => 'flag-waving',
        'iconColor' => '#ff891f',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_share'],
        ],
        'params' => [
            'module_name'   => 'page',
            'resource_name' => 'page',
        ],
        'menu'     => 'feed.itemShareActionsMenu',
        'name'     => 'share_on_page',
        'label'    => 'activity::phrase.share_on_page',
        'ordering' => 4,
        'value'    => 'shareOnPageProfile',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_pending', 0],
            ['truthy', 'item.extra.can_pin_item'],
            ['falsy', 'item.extra.can_review_feed'],
            ['truthy', 'profile_id'],
            ['or', ['eqeqeq', 'params._identity', '$.item.user'], ['eqeqeq', 'params._identity', '$.item.parent_user']],
            ['eq', 'profile_type', 'page'],
            ['noneOf', 'item.pins', '$.profile_id'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'pinItemOnPage',
        'label'    => 'page::phrase.pin_post_on_page',
        'ordering' => 2,
        'value'    => 'pinItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_pending', 0],
            ['truthy', 'item.extra.can_pin_item'],
            ['truthy', 'profile_id'],
            ['or', ['eqeqeq', 'params._identity', '$.item.user'], ['eqeqeq', 'params._identity', '$.item.parent_user']],
            ['eq', 'profile_type', 'page'],
            ['oneOf', 'item.pins', '$.profile_id'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'unpinItemOnPage',
        'label'    => 'activity::phrase.unpin_post',
        'ordering' => 3, 'value' => 'unpinItem',
    ],
];
