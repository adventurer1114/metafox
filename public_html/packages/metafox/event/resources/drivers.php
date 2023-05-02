<?php

/* this is auto generated file */
return [
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Category\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'event.category',
        'version'    => 'v1',
        'resolution' => 'admin',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Models\\Event',
        'type'       => 'entity',
        'name'       => 'event',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Events',
    ],
    [
        'driver'     => 'MetaFox\\Event\\Models\\Category',
        'type'       => 'entity',
        'name'       => 'event_category',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Event Categories',
    ],
    [
        'driver'     => 'MetaFox\\Event\\Models\\CategoryData',
        'type'       => 'entity',
        'name'       => 'event_category_data',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Models\\HostInvite',
        'type'       => 'entity',
        'name'       => 'event_host_invite',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Models\\Invite',
        'type'       => 'entity',
        'name'       => 'event_invite',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Models\\Member',
        'type'       => 'entity',
        'name'       => 'event_member',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Models\\EventText',
        'type'       => 'entity',
        'name'       => 'event_text',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Models\\Event',
        'type'       => 'entity-content',
        'name'       => 'event',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Events',
    ],
    [
        'driver'     => 'MetaFox\\Event\\Models\\Event',
        'type'       => 'entity-user',
        'name'       => 'event',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Category\\Admin\\DestroyCategoryForm',
        'type'       => 'form',
        'name'       => 'event.event_category.destroy',
        'version'    => 'v1',
        'resolution' => 'admin',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Category\\Admin\\StoreCategoryForm',
        'type'       => 'form',
        'name'       => 'event.event_category.store',
        'version'    => 'v1',
        'resolution' => 'admin',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Category\\Admin\\UpdateCategoryForm',
        'type'       => 'form',
        'name'       => 'event.event_category.update',
        'version'    => 'v1',
        'resolution' => 'admin',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\StoreEventMobileForm',
        'type'       => 'form',
        'name'       => 'event.event.store',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\UpdateEventMobileForm',
        'type'       => 'form',
        'name'       => 'event.event.update',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\HostInvite\\StoreInviteHostsMobileForm',
        'type'       => 'form',
        'name'       => 'event.invite_hosts.store',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Invite\\StoreInviteMobileForm',
        'type'       => 'form',
        'name'       => 'event.invite.store',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\MassEmailMobileForm',
        'type'       => 'form',
        'name'       => 'event.mass_email',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => true,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\MassEmailForm',
        'type'       => 'form',
        'name'       => 'event.mass_email',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => true,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\SearchEventForm',
        'type'       => 'form',
        'name'       => 'event.search',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => true,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\SearchEventMobileForm',
        'type'       => 'form',
        'name'       => 'event.search',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => true,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\SearchEventMapForm',
        'type'       => 'form',
        'name'       => 'event.search_map',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => true,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\SearchSimpleForm',
        'type'       => 'form',
        'name'       => 'event.search_simple',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => true,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\SettingForm',
        'type'       => 'form',
        'name'       => 'event.setting',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\SettingMobileForm',
        'type'       => 'form',
        'name'       => 'event.setting',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\CreateEventForm',
        'type'       => 'form',
        'name'       => 'event.store',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\EditEventForm',
        'type'       => 'form',
        'name'       => 'event.update',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Admin\\SiteSettingForm',
        'type'       => 'form-settings',
        'name'       => 'event',
        'version'    => 'v1',
        'resolution' => 'admin',
        'is_active'  => true,
        'is_preload' => false,
        'url'        => '/admincp/event/setting',
    ],
    [
        'driver'     => 'MetaFox\\Event\\Jobs\\DeleteCategoryJob',
        'type'       => 'job',
        'name'       => 'MetaFox\\Event\\Jobs\\DeleteCategoryJob',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Jobs\\UpdateStatusCodeInviteJob',
        'type'       => 'job',
        'name'       => 'MetaFox\\Event\\Jobs\\UpdateStatusCodeInviteJob',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Category\\CategoryItemCollection',
        'type'       => 'json-collection',
        'name'       => 'event_category.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\EventEmbedCollection',
        'type'       => 'json-collection',
        'name'       => 'event.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\HostInvite\\InviteItemCollection',
        'type'       => 'json-collection',
        'name'       => 'event_host_invite.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Invite\\InviteItemCollection',
        'type'       => 'json-collection',
        'name'       => 'event_invite.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\EventItemCollection',
        'type'       => 'json-collection',
        'name'       => 'event.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Member\\MemberEmbedCollection',
        'type'       => 'json-collection',
        'name'       => 'event_member.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Member\\MemberItemCollection',
        'type'       => 'json-collection',
        'name'       => 'event_member.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Category\\CategoryDetail',
        'type'       => 'json-resource',
        'name'       => 'event_category.detail',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Category\\CategoryItem',
        'type'       => 'json-resource',
        'name'       => 'event_category.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\EventDetail',
        'type'       => 'json-resource',
        'name'       => 'event.detail',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\EventEmbed',
        'type'       => 'json-resource',
        'name'       => 'event.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\HostInvite\\InviteItem',
        'type'       => 'json-resource',
        'name'       => 'event_host_invite.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\InviteCode\\InviteCodeItem',
        'type'       => 'json-resource',
        'name'       => 'event_invite_code.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Invite\\InviteDetail',
        'type'       => 'json-resource',
        'name'       => 'event_invite.detail',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Invite\\InviteItem',
        'type'       => 'json-resource',
        'name'       => 'event_invite.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\EventItem',
        'type'       => 'json-resource',
        'name'       => 'event.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Member\\MemberDetail',
        'type'       => 'json-resource',
        'name'       => 'event_member.detail',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Member\\MemberEmbed',
        'type'       => 'json-resource',
        'name'       => 'event_member.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Member\\MemberItem',
        'type'       => 'json-resource',
        'name'       => 'event_member.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Notifications\\HostInvite',
        'type'       => 'notification',
        'name'       => 'event_host_invite',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Notifications\\Invite',
        'type'       => 'notification',
        'name'       => 'event_invite',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Notifications\\NewEventDiscussion',
        'type'       => 'notification',
        'name'       => 'new_event_discussion',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\PackageSetting',
        'type'       => 'package-setting',
        'name'       => 'event',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Policies\\CategoryPolicy',
        'type'       => 'policy-resource',
        'name'       => 'MetaFox\\Event\\Models\\Category',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Policies\\EventPolicy',
        'type'       => 'policy-resource',
        'name'       => 'MetaFox\\Event\\Models\\Event',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Policy',
    ],
    [
        'driver'     => 'MetaFox\\Event\\Policies\\MemberPolicy',
        'type'       => 'policy-resource',
        'name'       => 'MetaFox\\Event\\Models\\Member',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'event',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Category\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'event.category',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\HostInvite\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'event_host_invite',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Invite\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'event_invite',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\InviteCode\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'event_invite_code',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Member\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'event_member',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Event\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'event',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\HostInvite\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'event_host_invite',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Invite\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'event_invite',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\InviteCode\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'event_invite_code',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Event\\Http\\Resources\\v1\\Member\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'event_member',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
];
