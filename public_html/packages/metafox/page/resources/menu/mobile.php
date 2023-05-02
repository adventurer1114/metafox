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
        'icon'     => 'ico-envelope-o',
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
        'icon'     => 'ico-check-circle-o',
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
        'icon'     => 'ico-sponsor',
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
        'icon'     => 'ico-sponsor',
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
        'icon'     => 'ico-sponsor',
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
        'icon'     => 'ico-sponsor',
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
        'icon'     => 'ico-diamond',
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
        'icon'     => 'ico-diamond',
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
        'icon'      => 'ico-trash',
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
        'ordering' => 4,
        'value'    => 'comingSoon',
        'icon'     => 'ico-comment-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_claim'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'claim',
        'label'    => 'page::phrase.claim_page',
        'ordering' => 5,
        'value'    => 'page/claim',
        'icon'     => 'ico-compose-alt',
    ],
    [
        'showWhen' => ['truthy', 'item.extra.can_edit'],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'manage',
        'label'    => 'page::phrase.manage',
        'ordering' => 6,
        'value'    => 'pages/manage',
        'icon'     => 'ico-gear-o',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'invite',
        'label'    => 'page::phrase.invite_friends',
        'ordering' => 7,
        'value'    => 'page/inviteFriends',
        'icon'     => 'ico-envelope-o',
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
        'ordering' => 8,
        'value'    => 'featureItem',
        'icon'     => 'ico-diamond-o',
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
        'icon'     => 'ico-diamond-o',
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
        'icon'     => 'ico-sponsor',
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
        'icon'     => 'ico-sponsor',
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
        'icon'     => 'ico-user2-next-o',
    ],
    [
        'showWhen' => [
            'or',
            ['truthy', 'item.extra.can_delete'],
        ],
        'className' => 'itemDelete',
        'menu'      => 'page.page.profileActionMenu',
        'name'      => 'delete',
        'label'     => 'page::phrase.delete',
        'ordering'  => 15,
        'value'     => 'deleteItem',
        'icon'      => 'ico-trash',
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
        'icon'     => 'ico-comment-o',
    ],
    [
        'showWhen' => [],
        'menu'     => 'page.page.profilePopoverMenu',
        'name'     => 'invite',
        'label'    => 'page::phrase.invite_friends',
        'ordering' => 2,
        'value'    => 'page/inviteFriends',
        'icon'     => 'ico-envelope-o',
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
        'icon'     => 'ico-user2-down-o',
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
        'icon'     => 'ico-businessman',
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
        'icon'     => 'ico-close-circle-o',
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
        'icon'     => 'ico-businessman',
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
        'icon'     => 'ico-ban',
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
        'icon'     => 'ico-user1-del-o',
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
        'icon'     => 'ico-hashtag',
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
        'icon'     => 'ico-user-man-o',
        'to'       => '/pages/my',
    ],
    [
        'params' => [
            'module_name'   => 'page',
            'resource_name' => 'page',
        ],
        'menu'     => 'page.sidebarMenu',
        'name'     => 'my_pending',
        'label'    => 'page::phrase.my_pending_pages',
        'ordering' => 3,
        'value'    => 'viewMyPendingPages',
        'icon'     => 'ico-clock-o',
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
        'icon'     => 'ico-user1-two-o',
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
        'icon'     => 'ico-thumbup-o',
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
        'icon'     => 'ico-envelope-o',
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
        'icon'     => 'ico-clock-o',
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
        'icon'     => 'ico-envelope-o',
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
        'icon'     => 'ico-compose-alt',
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
        'icon'     => 'ico-sponsor',
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
        'icon'     => 'ico-sponsor',
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
        'icon'     => 'ico-sponsor',
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
        'icon'     => 'ico-sponsor',
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
        'icon'     => 'ico-diamond',
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
        'icon'     => 'ico-diamond',
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
        'icon'      => 'ico-trash',
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
];