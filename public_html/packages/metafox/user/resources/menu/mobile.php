<?php

/* this is auto generated file */
return [
    [
        'tab'      => 'member',
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_view_members'],
        ],
        'menu'     => 'group.group.profileMenu',
        'name'     => 'member',
        'label'    => 'user::phrase.members',
        'ordering' => 4,
        'to'       => '/member',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.user.user.view'],
            ['falsy', 'is_hashtag'],
        ],
        'module_name'   => 'user',
        'resource_name' => 'user',
        'menu'          => 'search.mobileCategoryMenu',
        'name'          => 'user',
        'label'         => 'user::phrase.user_global_search_label',
        'ordering'      => 1,
        'value'         => 'searchGlobalUser',
    ],
    [
        'selectedWhen' => ['eq', 'item.sort_type', 1],
        'menu'         => 'user.shortcut.itemActionMenu',
        'name'         => 'auto',
        'label'        => 'user::phrase.sort_automatically',
        'ordering'     => 1,
        'value'        => 'user/shortcut/sortAutomatically',
        'icon'         => 'ico-star-o',
    ],
    [
        'selectedWhen' => ['eq', 'item.sort_type', 0],
        'menu'         => 'user.shortcut.itemActionMenu',
        'name'         => 'hide',
        'label'        => 'user::phrase.hide',
        'ordering'     => 1,
        'value'        => 'user/shortcut/hide',
        'icon'         => 'ico-eye-alt',
    ],
    [
        'selectedWhen' => ['eq', 'item.sort_type', 2],
        'menu'         => 'user.shortcut.itemActionMenu',
        'name'         => 'pin',
        'label'        => 'user::phrase.pin_to_top',
        'ordering'     => 2,
        'value'        => 'user/shortcut/pinToTop',
        'icon'         => 'ico-link',
    ],
    [
        'tab'      => 'all',
        'showWhen' => [
            'and',
            ['falsy', 'acl.user.user.isGuest'],
        ],
        'params' => [
            'module_name'   => 'user',
            'resource_name' => 'user',
        ],
        'menu'     => 'user.tabMenu',
        'name'     => 'all',
        'label'    => 'user::phrase.all_members',
        'ordering' => 1,
        'value'    => 'viewAll',
        'icon'     => 'ico ico-user1-three',
        'to'       => '/user/all',
    ],
    [
        'tab'      => 'recommend',
        'showWhen' => [
            'and',
            ['falsy', 'acl.user.user.isGuest'],
        ],
        'params' => [
            'module_name'   => 'user',
            'resource_name' => 'user',
        ],
        'menu'     => 'user.tabMenu',
        'name'     => 'recommend',
        'label'    => 'user::phrase.members_you_may_know',
        'ordering' => 2,
        'value'    => 'viewRecommendUsers',
        'icon'     => 'ico-user2-three-o',
        'to'       => '/user/recommend',
    ],
    [
        'tab'    => 'recent',
        'params' => [
            'module_name'   => 'user',
            'resource_name' => 'user',
        ],
        'menu'     => 'user.tabMenu',
        'name'     => 'recent',
        'label'    => 'user::phrase.recent_active',
        'ordering' => 3,
        'value'    => 'viewRecentUsers',
        'icon'     => 'ico-user2-clock-o',
        'to'       => '/user/recent',
    ],
    [
        'tab'    => 'featured',
        'params' => [
            'module_name'   => 'user',
            'resource_name' => 'user',
        ],
        'menu'     => 'user.tabMenu',
        'name'     => 'featured',
        'label'    => 'user::phrase.featured_members',
        'ordering' => 4,
        'value'    => 'viewFeaturedUsers',
        'icon'     => 'ico-diamond-o',
        'to'       => '/user/featured',
    ],
    [
        'action'        => 'filterMember',
        'resource_name' => 'user',
        'module_name'   => 'user',
        'menu'          => 'user.user.fab_buttons',
        'name'          => 'sort_menu',
        'label'         => 'core::phrase.filter',
        'ordering'      => 1,
        'icon'          => 'filter',
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
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'add_friend',
        'label'    => 'user::phrase.add_friend',
        'ordering' => 1,
        'value'    => 'user/addFriend',
        'icon'     => 'ico-user3-plus-o',
    ],
    [
        'showWhen' => ['eq', 'item.friendship', 3],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'cancel_request',
        'label'    => 'user::phrase.cancel_request',
        'ordering' => 2,
        'value'    => 'user/cancelRequest',
        'icon'     => 'ico-user2-del-o',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['eq', 'item.friendship', 2],
        ],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'confirm_request',
        'label'    => 'user::phrase.accept',
        'ordering' => 4,
        'value'    => 'user/acceptFriendRequest',
        'icon'     => 'ico-check',
    ],
    [
        'showWhen' => ['eq', 'item.friendship', 1],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'edit_list',
        'label'    => 'user::phrase.edit_list',
        'ordering' => 4,
        'value'    => 'friend/assignFriendList',
        'icon'     => 'ico-pencilline-o',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['eq', 'item.friendship', 2],
        ],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'delete_request',
        'label'    => 'user::phrase.delete',
        'ordering' => 5,
        'value'    => 'user/denyFriendRequest',
        'icon'     => 'ico-close',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['falsy', 'item.is_sponsored_feed'],
        ],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'sponsor_in_feed',
        'label'    => 'user::phrase.sponsor_in_feed',
        'ordering' => 6,
        'value'    => 'sponsorItemInFeed',
        'icon'     => 'ico-sponsor',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
        ],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'edit_profile',
        'label'    => 'user::phrase.edit_profile',
        'ordering' => 6,
        'value'    => 'user/editItem',
        'icon'     => '',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['truthy', 'item.is_sponsored_feed'],
        ],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'delete_sponsor_in_feed',
        'label'    => 'user::phrase.unsponsor_in_feed',
        'ordering' => 7,
        'value'    => 'unsponsorItemInFeed',
        'icon'     => 'ico-sponsor',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['falsy', 'item.is_sponsor'],
        ],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'sponsor',
        'label'    => 'user::phrase.sponsor_this_item',
        'ordering' => 8,
        'value'    => 'sponsorItem',
        'icon'     => 'ico-sponsor',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['truthy', 'item.is_sponsor'],
        ],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'delete_sponsor',
        'label'    => 'user::phrase.unsponsor_this_item',
        'ordering' => 9,
        'value'    => 'unsponsorItem',
        'icon'     => 'ico-sponsor',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
        ],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'feature',
        'label'    => 'user::phrase.feature',
        'ordering' => 10,
        'value'    => 'featureItem',
        'icon'     => 'ico-diamond',
    ],
    [
        'showWhen' => ['eq', 'item.friendship', 1],
        'style'    => 'danger',
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'unfriend',
        'label'    => 'user::phrase.unfriend',
        'ordering' => 11,
        'value'    => 'user/unFriend',
        'icon'     => 'ico-user3-minus-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
        ],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'delete_feature',
        'label'    => 'user::phrase.un_feature',
        'ordering' => 11,
        'value'    => 'unfeatureItem',
        'icon'     => 'ico-diamond',
    ],
    [
        'style'    => 'danger',
        'showWhen' => [
            'and',
            ['falsy', 'item.is_blocked'],
            ['neq', 'item.friendship', 5],
        ],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'block',
        'label'    => 'user::phrase.block_this_user',
        'ordering' => 12,
        'value'    => 'blockItem',
        'icon'     => 'ico-ban',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
        ],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'update_cover',
        'label'    => 'user::phrase.update_cover',
        'ordering' => 13,
        'value'    => '@app/EDIT_ITEM_COVER',
        'icon'     => '',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
        ],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'update_avatar',
        'label'    => 'user::phrase.update_avatar',
        'ordering' => 14,
        'value'    => '@app/EDIT_ITEM_AVATAR',
        'icon'     => '',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_remove_profile_cover'],
        ],
        'menu'      => 'user.user.itemActionMenu',
        'name'      => 'remove_cover',
        'label'     => 'user::phrase.remove_cover_photo',
        'ordering'  => 15,
        'value'     => 'removeCoverPhoto',
        'icon'      => '',
        'is_active' => 0,
        'is_delete' => 1,
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
        'menu'     => 'user.user.profileActionMenu',
        'name'     => 'add_friend',
        'label'    => 'user::phrase.add_friend',
        'ordering' => 2,
        'value'    => 'user/addFriend',
        'icon'     => 'ico-user3-plus-o',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.friendship', 3],
        ],
        'menu'     => 'user.user.profileActionMenu',
        'name'     => 'cancel_request',
        'label'    => 'user::phrase.cancel_request',
        'ordering' => 3,
        'value'    => 'user/cancelRequest',
        'icon'     => 'ico-user2-del-o',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['eq', 'item.friendship', 2],
        ],
        'menu'     => 'user.user.profileActionMenu',
        'name'     => 'confirm_request',
        'label'    => 'user::phrase.accept',
        'ordering' => 4,
        'value'    => 'user/acceptFriendRequest',
        'icon'     => 'ico-check',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['eq', 'item.friendship', 2],
        ],
        'menu'     => 'user.user.profileActionMenu',
        'name'     => 'delete_request',
        'label'    => 'user::phrase.delete',
        'ordering' => 5,
        'value'    => 'user/denyFriendRequest',
        'icon'     => 'ico-close',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
        ],
        'menu'     => 'user.user.profileActionMenu',
        'name'     => 'edit_profile',
        'label'    => 'user::phrase.edit_profile',
        'ordering' => 6,
        'value'    => 'user/editProfile',
        'icon'     => 'ico-pencil',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_message'],
        ],
        'menu'       => 'user.user.profileActionMenu',
        'name'       => 'message',
        'label'      => 'user::phrase.message',
        'ordering'   => 7,
        'value'      => 'chat/room/openChatRoom',
        'icon'       => 'ico-comment-o',
        'is_active'  => 0,
        'is_deleted' => 1,
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_poke'],
            ['neq', 'item.friendship', 5],
        ],
        'menu'      => 'user.user.profileActionMenu',
        'name'      => 'poke',
        'label'     => 'user::phrase.poke',
        'ordering'  => 8,
        'value'     => 'pokeItem',
        'icon'      => 'ico-smile-o',
        'is_active' => 0,
        'is_delete' => 1,
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_feature'],
            ['falsy', 'item.is_featured'],
        ],
        'menu'     => 'user.user.profileActionMenu',
        'name'     => 'feature',
        'label'    => 'user::phrase.featured_this_user',
        'ordering' => 9,
        'value'    => 'featureItem',
        'icon'     => 'ico-diamond-o',
    ],
    [
        'style'    => 'danger',
        'showWhen' => [
            'and',
            ['eq', 'item.friendship', 1],
        ],
        'menu'     => 'user.user.profileActionMenu',
        'name'     => 'un_friend',
        'label'    => 'user::phrase.un_friend',
        'ordering' => 10,
        'value'    => 'user/unFriend',
        'icon'     => 'ico-user3-minus-o',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_feature'],
            ['truthy', 'item.is_featured'],
        ],
        'menu'     => 'user.user.profileActionMenu',
        'name'     => 'unfeature',
        'label'    => 'user::phrase.un_featured_this_user',
        'ordering' => 10,
        'value'    => 'unfeatureItem',
        'icon'     => 'ico-diamond-o',
    ],
    [
        'style'    => 'danger',
        'showWhen' => [
            'and',
            ['falsy', 'item.is_blocked'],
            ['neq', 'item.friendship', 5],
            ['truthy', 'item.extra.can_add'],
        ],
        'menu'     => 'user.user.profileActionMenu',
        'name'     => 'block',
        'label'    => 'user::phrase.block_this_user',
        'ordering' => 11,
        'value'    => 'blockItem',
        'icon'     => 'ico-ban',
    ],
    [
        'style'    => 'primary',
        'showWhen' => [
            'and',
            ['truthy', 'item.is_blocked'],
            ['neq', 'item.friendship', 5],
        ],
        'menu'     => 'user.user.profileActionMenu',
        'name'     => 'unblock',
        'label'    => 'user::phrase.un_block_this_user',
        'ordering' => 12,
        'value'    => 'unblockItem',
        'icon'     => 'ico-ban',
    ],
    [
        'active'   => true,
        'tab'      => 'home',
        'menu'     => 'user.user.profileMenu',
        'name'     => 'home',
        'label'    => 'user::phrase.overview',
        'ordering' => 1,
        'to'       => '/',
    ],
    [
        'tab'      => 'about',
        'showWhen' => [
            'and',
            ['truthy', 'item.profile_settings.profile_profile_info'],
        ],
        'menu'     => 'user.user.profileMenu',
        'name'     => 'about',
        'label'    => 'user::phrase.about',
        'ordering' => 2,
        'to'       => '/about',
    ],
    [
        'showWhen' => ['eq', 'item.friendship', 3],
        'menu'     => 'user.user.profilePopoverMenu',
        'name'     => 'cancel_request',
        'label'    => 'user::phrase.cancel_request',
        'ordering' => 1,
        'value'    => 'user/cancelRequest',
        'icon'     => 'ico-user2-del-o',
    ],
    [
        'showWhen' => ['eq', 'item.friendship', 1],
        'menu'     => 'user.user.profilePopoverMenu',
        'name'     => 'edit_list',
        'label'    => 'user::phrase.edit_list',
        'ordering' => 2,
        'value'    => 'friend/assignFriendList',
        'icon'     => 'ico-pencilline-o',
    ],
    [
        'showWhen' => ['eq', 'item.friendship', 1],
        'style'    => 'danger',
        'menu'     => 'user.user.profilePopoverMenu',
        'name'     => 'delete',
        'label'    => 'user::phrase.unfriend',
        'ordering' => 3,
        'value'    => 'user/unFriend',
        'icon'     => 'ico ico-user3-minus-o',
    ],
    [
        'style'    => 'danger',
        'showWhen' => [
            'and',
            ['falsy', 'item.is_blocked'],
            ['neq', 'item.friendship', 5],
        ],
        'menu'     => 'user.user.profilePopoverMenu',
        'name'     => 'block',
        'label'    => 'user::phrase.block_this_user',
        'ordering' => 4,
        'value'    => 'blockItem',
        'icon'     => 'ico-ban',
    ],
    [
        'menu'     => 'user.user.sort_menu',
        'name'     => 'full_name',
        'label'    => 'core::phrase.name',
        'ordering' => 1,
        'value'    => 'full_name',
    ],
    [
        'menu'     => 'user.user.sort_menu',
        'name'     => 'last_login',
        'label'    => 'user::phrase.last_login',
        'ordering' => 2,
        'value'    => 'last_login',
    ],
    [
        'menu'     => 'user.user.sort_menu',
        'name'     => 'last_activity',
        'label'    => 'user::phrase.last_activity',
        'ordering' => 2,
        'value'    => 'last_activity',
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
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'add_friend',
        'label'    => 'user::phrase.add_friend',
        'ordering' => 1,
        'value'    => 'user/addFriend',
        'icon'     => 'ico-user3-plus-o',
    ],
    [
        'showWhen' => ['eq', 'item.friendship', 3],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'cancel_request',
        'label'    => 'user::phrase.cancel_request',
        'ordering' => 2,
        'value'    => 'user/cancelRequest',
        'icon'     => 'ico-user2-del-o',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['eq', 'item.friendship', 2],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'confirm_request',
        'label'    => 'user::phrase.accept',
        'ordering' => 4,
        'value'    => 'user/acceptFriendRequest',
        'icon'     => 'ico-check',
    ],
    [
        'showWhen' => ['eq', 'item.friendship', 1],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'edit_list',
        'label'    => 'user::phrase.edit_list',
        'ordering' => 4,
        'value'    => 'friend/assignFriendList',
        'icon'     => 'ico-pencilline-o',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['eq', 'item.friendship', 2],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'delete_request',
        'label'    => 'user::phrase.delete',
        'ordering' => 5,
        'value'    => 'user/denyFriendRequest',
        'icon'     => 'ico-close',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['falsy', 'item.is_sponsored_feed'],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'sponsor_in_feed',
        'label'    => 'user::phrase.sponsor_in_feed',
        'ordering' => 6,
        'value'    => 'sponsorItemInFeed',
        'icon'     => 'ico-sponsor',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'edit_profile',
        'label'    => 'user::phrase.edit_profile',
        'ordering' => 6,
        'value'    => 'user/editItem',
        'icon'     => '',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['truthy', 'item.is_sponsored_feed'],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'delete_sponsor_in_feed',
        'label'    => 'user::phrase.unsponsor_in_feed',
        'ordering' => 7,
        'value'    => 'unsponsorItemInFeed',
        'icon'     => 'ico-sponsor',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['falsy', 'item.is_sponsor'],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'sponsor',
        'label'    => 'user::phrase.sponsor_this_item',
        'ordering' => 8,
        'value'    => 'sponsorItem',
        'icon'     => 'ico-sponsor',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['truthy', 'item.is_sponsor'],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'delete_sponsor',
        'label'    => 'user::phrase.unsponsor_this_item',
        'ordering' => 9,
        'value'    => 'unsponsorItem',
        'icon'     => 'ico-sponsor',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'feature',
        'label'    => 'user::phrase.feature',
        'ordering' => 10,
        'value'    => 'featureItem',
        'icon'     => 'ico-diamond',
    ],
    [
        'showWhen' => ['eq', 'item.friendship', 1],
        'style'    => 'danger',
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'unfriend',
        'label'    => 'user::phrase.unfriend',
        'ordering' => 11,
        'value'    => 'user/unFriend',
        'icon'     => 'ico-user3-minus-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'delete_feature',
        'label'    => 'user::phrase.un_feature',
        'ordering' => 11,
        'value'    => 'unfeatureItem',
        'icon'     => 'ico-diamond',
    ],
    [
        'style'    => 'danger',
        'showWhen' => [
            'and',
            ['falsy', 'item.is_blocked'],
            ['neq', 'item.friendship', 5],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'block',
        'label'    => 'user::phrase.block_this_user',
        'ordering' => 12,
        'value'    => 'blockItem',
        'icon'     => 'ico-ban',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'update_cover',
        'label'    => 'user::phrase.update_cover',
        'ordering' => 13,
        'value'    => '@app/EDIT_ITEM_COVER',
        'icon'     => '',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'update_avatar',
        'label'    => 'user::phrase.update_avatar',
        'ordering' => 14,
        'value'    => '@app/EDIT_ITEM_AVATAR',
        'icon'     => '',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_remove_profile_cover'],
        ],
        'menu'      => 'user.user.detailActionMenu',
        'name'      => 'remove_cover',
        'label'     => 'user::phrase.remove_cover_photo',
        'ordering'  => 15,
        'value'     => 'removeCoverPhoto',
        'icon'      => '',
        'is_active' => 0,
        'is_delete' => 1,
    ],
    [
        'showWhen'  => [],
        'menu'      => 'core.bodyMenu',
        'name'      => 'member',
        'label'     => 'user::phrase.members',
        'ordering'  => 10,
        'value'     => '',
        'to'        => '/user',
        'as'        => 'item',
        'icon'      => 'user1-three',
        'iconColor' => '#1d6f96',
    ],
];
