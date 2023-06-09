<?php

/* this is auto generated file */
return [
    [
        'driver'     => 'MetaFox\\Saved\\Models\\Saved',
        'type'       => 'entity',
        'name'       => 'saved',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Saved Items',
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Models\\SavedAgg',
        'type'       => 'entity',
        'name'       => 'saved_aggregation',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Models\\SavedList',
        'type'       => 'entity',
        'name'       => 'saved_list',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Saved Lists',
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Models\\SavedListData',
        'type'       => 'entity',
        'name'       => 'saved_list_data',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Saved List Data',
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Models\\SavedSearchItem',
        'type'       => 'entity',
        'name'       => 'saved_search_item',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Models\\SavedListMember',
        'type'       => 'entity',
        'name'       => 'saved_list_member',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedList\\AddToCollectionMobileForm',
        'type'       => 'form',
        'name'       => 'saved.saved.add_to_collection',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedList\\AddFriendForm',
        'type'       => 'form',
        'name'       => 'saved.saved_list.add_friend',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedList\\StoreSavedListForm',
        'type'       => 'form',
        'name'       => 'saved.saved_list.store',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedList\\StoreSavedListMobileForm',
        'type'       => 'form',
        'name'       => 'saved.saved_list.store',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedList\\UpdateSavedListMobileForm',
        'type'       => 'form',
        'name'       => 'saved.saved_list.update',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedList\\UpdateSavedListForm',
        'type'       => 'form',
        'name'       => 'saved.saved_list.update',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\Saved\\SearchSavedMobileForm',
        'type'       => 'form',
        'name'       => 'saved.search',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => true,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\Saved\\SearchSavedForm',
        'type'       => 'form',
        'name'       => 'saved.search',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => true,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\Saved\\SideBarFilterForm',
        'type'       => 'form',
        'name'       => 'saved.sidebar',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => true,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\Admin\\SiteSettingForm',
        'type'       => 'form-settings',
        'name'       => 'saved',
        'version'    => 'v1',
        'resolution' => 'admin',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'core::phrase.settings',
        'url'        => '/admincp/saved/setting',
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\Saved\\SavedEmbedCollection',
        'type'       => 'json-collection',
        'name'       => 'saved.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\Saved\\SavedItemCollection',
        'type'       => 'json-collection',
        'name'       => 'saved.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedList\\SavedListEmbedCollection',
        'type'       => 'json-collection',
        'name'       => 'saved_list.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedList\\SavedListItemCollection',
        'type'       => 'json-collection',
        'name'       => 'saved_list.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\Saved\\SavedDetail',
        'type'       => 'json-resource',
        'name'       => 'saved.detail',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\Saved\\SavedEmbed',
        'type'       => 'json-resource',
        'name'       => 'saved.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\Saved\\SavedItem',
        'type'       => 'json-resource',
        'name'       => 'saved.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedList\\SavedListDetail',
        'type'       => 'json-resource',
        'name'       => 'saved_list.detail',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedList\\SavedListEmbed',
        'type'       => 'json-resource',
        'name'       => 'saved_list.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedList\\SavedListItem',
        'type'       => 'json-resource',
        'name'       => 'saved_list.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedListMember\\MemberItem',
        'type'       => 'json-resource',
        'name'       => 'saved_list_member.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\PackageSetting',
        'type'       => 'package-setting',
        'name'       => 'saved',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Policies\\SavedPolicy',
        'type'       => 'policy-resource',
        'name'       => 'MetaFox\\Saved\\Models\\Saved',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Policies\\SavedListPolicy',
        'type'       => 'policy-resource',
        'name'       => 'MetaFox\\Saved\\Models\\SavedList',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Policies\\Handlers\\IsSavedItem',
        'type'       => 'policy-rule',
        'name'       => 'isSavedItem',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Policies\\Handlers\\CanSaveItem',
        'type'       => 'policy-rule',
        'name'       => 'saveItem',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\Saved\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'saved',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedList\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'saved_list',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedListMember\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'saved_list_member',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\Saved\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'saved',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedList\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'saved_list',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Saved\\Http\\Resources\\v1\\SavedListMember\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'saved_list_member',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
];
