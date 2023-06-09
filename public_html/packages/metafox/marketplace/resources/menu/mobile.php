<?php

/* this is auto generated file */

use MetaFox\Platform\Support\Browse\Browse;

return [
    [
        'tab'      => 'marketplace',
        'showWhen' => [
            'or',
            ['eq', 'item.reg_method', '0'],
            ['truthy', 'acl.marketplace.marketplace.moderate'],
            ['truthy', 'item.is_member'],
        ],
        'menu'     => 'group.group.profileMenu',
        'name'     => 'marketplace',
        'label'    => 'marketplace::phrase.marketplace',
        'ordering' => 8,
        'to'       => '/listing',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'edit',
        'label'    => 'marketplace::phrase.edit',
        'ordering' => 1,
        'value'    => 'editItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['falsy', 'item.is_sponsored_feed'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'sponsor_in_feed',
        'label'    => 'marketplace::phrase.sponsor_in_feed',
        'ordering' => 2,
        'value'    => 'sponsorItemInFeed',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['truthy', 'item.is_sponsored_feed'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'unsponsor_in_feed',
        'label'    => 'marketplace::phrase.unsponsor_in_feed',
        'ordering' => 3,
        'value'    => 'unsponsorItemInFeed',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['falsy', 'item.is_sponsor'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'sponsor',
        'label'    => 'marketplace::phrase.sponsor_this_item',
        'ordering' => 4,
        'value'    => 'sponsorItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['truthy', 'item.is_sponsor'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'unsponsor',
        'label'    => 'marketplace::phrase.unsponsor_this_item',
        'ordering' => 5,
        'value'    => 'unsponsorItem',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'feature',
        'label'    => 'core::phrase.feature',
        'ordering' => 6,
        'value'    => 'featureItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'unfeature',
        'label'    => 'core::phrase.un_feature',
        'ordering' => 7,
        'value'    => 'unfeatureItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_pending'],
            ['truthy', 'item.extra.can_approve'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'approve',
        'label'    => 'marketplace::phrase.approve',
        'ordering' => 8,
        'value'    => 'approveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_invite'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'manage_invite',
        'label'    => 'marketplace::phrase.manage_invites',
        'ordering' => 9,
        'value'    => 'marketplace/viewInvitedPeople',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_reopen'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'reopen',
        'label'    => 'marketplace::phrase.reopen',
        'ordering' => 10,
        'value'    => 'marketplace/reopenItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_delete'],
        ],
        'className' => 'itemDelete',
        'style'     => 'danger',
        'menu'      => 'marketplace.marketplace.detailActionMenu',
        'name'      => 'delete',
        'label'     => 'marketplace::phrase.delete',
        'ordering'  => 20,
        'value'     => 'deleteItem',
    ],
    [
        'tab'      => 'marketplace',
        'showWhen' => [
            'and',
            ['truthy', 'acl.marketplace.marketplace.create'],
            ['truthy', 'item.is_owner'],
        ],
        'menu'     => 'marketplace.marketplace.headerItemActionOnUserProfileMenu',
        'name'     => 'marketplace',
        'label'    => 'marketplace::phrase.add_new_listing',
        'ordering' => 1,
        'to'       => '/marketplace/add?owner_id=:id',
    ],
    [
        'tab'      => 'marketplace',
        'showWhen' => [
            'and',
            ['truthy', 'acl.marketplace.marketplace.create'],
            ['truthy', 'item.profile_settings.marketplace_share_marketplace_listings'],
        ],
        'menu'     => 'marketplace.marketplace.headerItemActionOnPageProfileMenu',
        'name'     => 'marketplace',
        'label'    => 'marketplace::phrase.add_new_listing',
        'ordering' => 1,
        'to'       => '/marketplace/add?owner_id=:id',
    ],
    [
        'tab'      => 'marketplace',
        'showWhen' => [
            'and',
            ['truthy', 'acl.marketplace.marketplace.create'],
            ['truthy', 'item.profile_settings.marketplace_share_marketplace_listings'],
            ['falsy', 'item.is_muted'],
        ],
        'menu'     => 'marketplace.marketplace.headerItemActionOnGroupProfileMenu',
        'name'     => 'marketplace',
        'label'    => 'marketplace::phrase.add_new_listing',
        'ordering' => 1,
        'to'       => '/marketplace/add?owner_id=:id',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
        ],
        'menu'     => 'marketplace.marketplace.itemActionMenu',
        'name'     => 'edit',
        'label'    => 'marketplace::phrase.edit',
        'ordering' => 1,
        'value'    => 'editItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['falsy', 'item.is_sponsored_feed'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.itemActionMenu',
        'name'     => 'sponsor_in_feed',
        'label'    => 'marketplace::phrase.sponsor_in_feed',
        'ordering' => 2,
        'value'    => 'sponsorItemInFeed',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['truthy', 'item.is_sponsored_feed'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.itemActionMenu',
        'name'     => 'unsponsor_in_feed',
        'label'    => 'marketplace::phrase.unsponsor_in_feed',
        'ordering' => 3,
        'value'    => 'unsponsorItemInFeed',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['falsy', 'item.is_sponsor'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.itemActionMenu',
        'name'     => 'sponsor',
        'label'    => 'marketplace::phrase.sponsor_this_item',
        'ordering' => 4,
        'value'    => 'sponsorItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['truthy', 'item.is_sponsor'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.itemActionMenu',
        'name'     => 'unsponsor',
        'label'    => 'marketplace::phrase.unsponsor_this_item',
        'ordering' => 5,
        'value'    => 'unsponsorItem',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.itemActionMenu',
        'name'     => 'feature',
        'label'    => 'core::phrase.feature',
        'ordering' => 6,
        'value'    => 'featureItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.itemActionMenu',
        'name'     => 'unfeature',
        'label'    => 'core::phrase.un_feature',
        'ordering' => 7,
        'value'    => 'unfeatureItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_pending'],
            ['truthy', 'item.extra.can_approve'],
        ],
        'menu'     => 'marketplace.marketplace.itemActionMenu',
        'name'     => 'approve',
        'label'    => 'marketplace::phrase.approve',
        'ordering' => 8,
        'value'    => 'approveItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_reopen'],
        ],
        'menu'     => 'marketplace.marketplace.itemActionMenu',
        'name'     => 'reopen',
        'label'    => 'marketplace::phrase.reopen',
        'ordering' => 9,
        'value'    => 'marketplace/reopenItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_delete'],
        ],
        'className' => 'itemDelete',
        'style'     => 'danger',
        'menu'      => 'marketplace.marketplace.itemActionMenu',
        'name'      => 'delete',
        'label'     => 'marketplace::phrase.delete',
        'ordering'  => 20,
        'value'     => 'deleteItem',
    ],
    [
        'tab'      => 'marketplace',
        'showWhen' => [
            'and',
            ['truthy', 'acl.marketplace.marketplace.view'],
            ['truthy', 'item.profile_settings.marketplace_view_browse_marketplace_listings'],
        ],
        'menu'     => 'page.page.profileMenu',
        'name'     => 'marketplace',
        'label'    => 'marketplace::phrase.marketplace',
        'ordering' => 8,
        'to'       => '/listing',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.marketplace.marketplace.view'],
            ['falsy', 'is_hashtag'],
        ],
        'module_name'   => 'marketplace',
        'resource_name' => 'marketplace',
        'menu'          => 'search.mobileCategoryMenu',
        'name'          => 'marketplace',
        'label'         => 'marketplace::phrase.marketplace_global_search_label',
        'ordering'      => 7,
        'value'         => 'searchGlobalListing',
    ],
    [
        'tab'      => 'marketplace',
        'showWhen' => [
            'and',
            ['truthy', 'acl.marketplace.marketplace.view'],
            ['truthy', 'item.profile_settings.profile_view_profile'],
            ['truthy', 'item.profile_menu_settings.marketplace_profile_menu'],
        ],
        'menu'     => 'user.user.profileMenu',
        'name'     => 'marketplace',
        'label'    => 'marketplace::phrase.marketplace',
        'ordering' => 8,
        'to'       => '/listing',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.marketplace.marketplace.view'],
        ],
        'module_name'   => 'marketplace',
        'resource_name' => 'marketplace',
        'menu'          => 'group.mobileCategoryMenu',
        'name'          => 'marketplace',
        'label'         => 'marketplace::phrase.marketplace_global_search_label',
        'ordering'      => 7,
        'value'         => 'searchGlobalListing',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.marketplace.marketplace.view'],
        ],
        'module_name'   => 'marketplace',
        'resource_name' => 'marketplace',
        'menu'          => 'page.mobileCategoryMenu',
        'name'          => 'marketplace',
        'label'         => 'marketplace::phrase.marketplace_global_search_label',
        'ordering'      => 7,
        'value'         => 'searchGlobalListing',
    ],
    [
        'tab'    => 'all',
        'params' => [
            'module_name'   => 'marketplace',
            'resource_name' => 'marketplace',
        ],
        'menu'     => 'marketplace.sidebarMenu',
        'name'     => 'all',
        'label'    => 'marketplace::phrase.all_listings',
        'ordering' => 1,
        'to'       => '/marketplace/all',
        'value'    => 'viewAll',
    ],
    [
        'tab'    => 'my',
        'params' => [
            'module_name'   => 'marketplace',
            'resource_name' => 'marketplace',
        ],
        'menu'     => 'marketplace.sidebarMenu',
        'name'     => 'my',
        'label'    => 'marketplace::phrase.my_listings',
        'ordering' => 2,
        'to'       => '/marketplace/my',
        'value'    => 'viewMyListings',
    ],
    [
        'tab'    => 'my_expire',
        'params' => [
            'module_name'   => 'marketplace',
            'resource_name' => 'marketplace',
        ],
        'menu'     => 'marketplace.sidebarMenu',
        'name'     => 'my_expire',
        'label'    => 'marketplace::phrase.my_expired_listings',
        'ordering' => 3,
        'value'    => 'viewMyExpiredListings',
    ],
    [
        'tab'    => 'friend',
        'params' => [
            'module_name'   => 'marketplace',
            'resource_name' => 'marketplace',
        ],
        'menu'     => 'marketplace.sidebarMenu',
        'name'     => 'friends',
        'label'    => 'marketplace::phrase.friends_listings',
        'ordering' => 5,
        'to'       => '/marketplace/friend',
        'value'    => 'viewFriendsListings',
    ],
    [
        'tab'      => 'invite',
        'menu'     => 'marketplace.sidebarMenu',
        'name'     => 'invite',
        'label'    => 'marketplace::phrase.listing_invites',
        'ordering' => 6,
        'to'       => '/marketplace/invite',
        'value'    => 'viewListingInvites',
        'params'   => [
            'module_name'   => 'marketplace',
            'resource_name' => 'marketplace',
        ],
    ],
    [
        'tab'    => 'history',
        'params' => [
            'module_name'   => 'marketplace',
            'resource_name' => 'marketplace',
        ],
        'menu'     => 'marketplace.sidebarMenu',
        'name'     => 'history',
        'label'    => 'marketplace::phrase.history',
        'ordering' => 10,
        'to'       => '/marketplace/history',
        'value'    => 'viewHistory',
    ],
    [
        'tab'      => 'expire',
        'showWhen' => [
            'and',
            ['truthy', 'setting.marketplace.days_to_expire'],
            [
                'or',
                ['truthy', 'acl.marketplace.marketplace.moderate'],
                ['truthy', 'acl.marketplace.marketplace.view_expired'],
            ],
        ],
        'params' => [
            'module_name'   => 'marketplace',
            'resource_name' => 'marketplace',
        ],
        'menu'     => 'marketplace.sidebarMenu',
        'name'     => 'expire',
        'label'    => 'marketplace::phrase.expired',
        'ordering' => 8,
        'to'       => '/marketplace/expire',
        'value'    => 'viewExpired',
    ],
    [
        'tab'      => 'pending',
        'showWhen' => [
            'and',
            ['truthy', 'acl.marketplace.marketplace.approve'],
        ],
        'params' => [
            'module_name'   => 'marketplace',
            'resource_name' => 'marketplace',
        ],
        'menu'     => 'marketplace.sidebarMenu',
        'name'     => 'pending',
        'label'    => 'marketplace::phrase.pending_listings',
        'ordering' => 7,
        'to'       => '/marketplace/pending',
        'value'    => 'viewPendingListings',
    ],
    [
        'tab'    => 'my_pending',
        'params' => [
            'module_name'   => 'marketplace',
            'resource_name' => 'marketplace',
        ],
        'showWhen' => [
            'and',
            ['neq', 'session.user.role.id', 1],
        ],
        'menu'     => 'marketplace.sidebarMenu',
        'name'     => 'my_pending',
        'label'    => 'marketplace::phrase.my_pending_listings',
        'ordering' => 4,
        'to'       => '/marketplace/my-pending',
        'value'    => 'viewMyPendingListings',
    ],
    [
        'tab'    => 'invoice',
        'params' => [
            'module_name'   => 'marketplace',
            'resource_name' => 'marketplace_invoice',
        ],
        'menu'     => 'marketplace.sidebarMenu',
        'name'     => 'invoice',
        'label'    => 'marketplace::phrase.invoices',
        'ordering' => 9,
        'to'       => '/marketplace/invoice',
        'value'    => 'viewInvoices',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_invite'],
        ],
        'menu'       => 'marketplace.marketplace.itemActionMenu',
        'name'       => 'invite_people',
        'label'      => 'core::web.invite_friends',
        'ordering'   => 1,
        'value'      => 'marketplace/invitePeopleToCome',
        'is_active'  => 0,
        'is_deleted' => 1,
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_invite'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'invite_people',
        'label'    => 'core::web.invite_friends',
        'ordering' => 11,
        'value'    => 'marketplace/invitePeopleToCome',
    ],
    [
        'menu'     => 'marketplace.marketplace.filter_menu',
        'name'     => 'all',
        'label'    => 'core::phrase.when.all',
        'ordering' => 1,
        'value'    => Browse::WHEN_ALL,
    ],
    [
        'menu'     => 'marketplace.marketplace.filter_menu',
        'name'     => 'this_month',
        'label'    => 'core::phrase.when.this_month',
        'ordering' => 2,
        'value'    => Browse::WHEN_THIS_MONTH,
    ],
    [
        'menu'     => 'marketplace.marketplace.filter_menu',
        'name'     => 'this_week',
        'label'    => 'core::phrase.when.this_week',
        'ordering' => 3,
        'value'    => Browse::WHEN_THIS_WEEK,
    ],
    [
        'menu'     => 'marketplace.marketplace.filter_menu',
        'name'     => 'today',
        'label'    => 'core::phrase.when.today',
        'ordering' => 4,
        'value'    => Browse::WHEN_TODAY,
    ],
    [
        'menu'     => 'marketplace.marketplace.sort_menu',
        'name'     => 'latest',
        'label'    => 'core::phrase.sort.recent',
        'ordering' => 1,
        'value'    => Browse::SORT_RECENT,
    ],
    [
        'menu'     => 'marketplace.marketplace.sort_menu',
        'name'     => 'most_viewed',
        'label'    => 'core::phrase.sort.most_viewed',
        'ordering' => 2,
        'value'    => Browse::SORT_MOST_VIEWED,
    ],
    [
        'menu'     => 'marketplace.marketplace.sort_menu',
        'name'     => 'most_liked',
        'label'    => 'core::phrase.sort.most_liked',
        'ordering' => 3,
        'value'    => Browse::SORT_MOST_LIKED,
    ],
    [
        'menu'     => 'marketplace.marketplace.sort_menu',
        'name'     => 'most_discussed',
        'label'    => 'core::phrase.sort.most_discussed',
        'ordering' => 4,
        'value'    => Browse::SORT_MOST_DISCUSSED,
    ],
    [
        'menu'     => 'marketplace.marketplace_invoice.filter_menu',
        'name'     => 'all',
        'label'    => 'core::phrase.when.all',
        'ordering' => 1,
        'value'    => Browse::WHEN_ALL,
    ],
    [
        'menu'     => 'marketplace.marketplace_invoice.filter_menu',
        'name'     => 'this_month',
        'label'    => 'core::phrase.when.this_month',
        'ordering' => 2,
        'value'    => Browse::WHEN_THIS_MONTH,
    ],
    [
        'menu'     => 'marketplace.marketplace_invoice.filter_menu',
        'name'     => 'this_week',
        'label'    => 'core::phrase.when.this_week',
        'ordering' => 3,
        'value'    => Browse::WHEN_THIS_WEEK,
    ],
    [
        'menu'     => 'marketplace.marketplace_invoice.filter_menu',
        'name'     => 'today',
        'label'    => 'core::phrase.when.today',
        'ordering' => 4,
        'value'    => Browse::WHEN_TODAY,
    ],
    [
        'menu'     => 'marketplace.marketplace_invoice.sort_menu',
        'name'     => 'latest',
        'label'    => 'core::phrase.sort.recent',
        'ordering' => 1,
        'value'    => Browse::SORT_RECENT,
    ],
    [
        'menu'     => 'marketplace.marketplace_invoice.sort_menu',
        'name'     => 'most_viewed',
        'label'    => 'core::phrase.sort.most_viewed',
        'ordering' => 2,
        'value'    => Browse::SORT_MOST_VIEWED,
    ],
    [
        'menu'     => 'marketplace.marketplace_invoice.sort_menu',
        'name'     => 'most_liked',
        'label'    => 'core::phrase.sort.most_liked',
        'ordering' => 3,
        'value'    => Browse::SORT_MOST_LIKED,
    ],
    [
        'menu'     => 'marketplace.marketplace_invoice.sort_menu',
        'name'     => 'most_discussed',
        'label'    => 'core::phrase.sort.most_discussed',
        'ordering' => 4,
        'value'    => Browse::SORT_MOST_DISCUSSED,
    ],
    [
        'showWhen'  => [],
        'menu'      => 'core.bodyMenu',
        'name'      => 'marketplace',
        'label'     => 'marketplace::phrase.marketplace',
        'ordering'  => 6,
        'value'     => '',
        'to'        => '/marketplace',
        'as'        => 'item',
        'icon'      => 'shopbasket',
        'iconColor' => '#a1560f',
    ],
    [
        'menu'     => 'marketplace.view_map.filter_menu',
        'name'     => 'all',
        'label'    => 'core::phrase.when.all',
        'ordering' => 1,
        'value'    => 'all',
    ],
    [
        'menu'     => 'marketplace.view_map.filter_menu',
        'name'     => 'this_month',
        'label'    => 'core::phrase.when.this_month',
        'ordering' => 2,
        'value'    => 'this_month',
    ],
    [
        'menu'     => 'marketplace.view_map.filter_menu',
        'name'     => 'this_week',
        'label'    => 'core::phrase.when.this_week',
        'ordering' => 3,
        'value'    => 'this_week',
    ],
    [
        'menu'     => 'marketplace.view_map.filter_menu',
        'name'     => 'today',
        'label'    => 'core::phrase.when.today',
        'ordering' => 4,
        'value'    => 'today',
    ],
    [
        'menu'     => 'marketplace.view_map.sort_menu',
        'name'     => 'new',
        'label'    => 'core::phrase.new',
        'ordering' => 1,
        'value'    => 'desc',
    ],
    [
        'menu'     => 'marketplace.view_map.sort_menu',
        'name'     => 'latest',
        'label'    => 'core::phrase.sort.latest',
        'ordering' => 2,
        'value'    => 'asc',
    ],
    [
        'menu'     => 'marketplace.view_map.nearest',
        'name'     => 'view_5_nearest',
        'label'    => 'marketplace::phrase.nearest.view_5_nearest_listings',
        'ordering' => 1,
        'value'    => 5,
    ],
    [
        'menu'     => 'marketplace.view_map.nearest',
        'name'     => 'view_10_nearest',
        'label'    => 'marketplace::phrase.nearest.view_10_nearest_listings',
        'ordering' => 2,
        'value'    => 10,
    ],
    [
        'menu'     => 'marketplace.view_map.nearest',
        'name'     => 'view_15_nearest',
        'label'    => 'marketplace::phrase.nearest.view_15_nearest_listings',
        'ordering' => 3,
        'value'    => 15,
    ],
    [
        'menu'     => 'marketplace.view_map.nearest',
        'name'     => 'view_20_nearest',
        'label'    => 'marketplace::phrase.nearest.view_20_nearest_listings',
        'ordering' => 4,
        'value'    => 20,
    ],
];
