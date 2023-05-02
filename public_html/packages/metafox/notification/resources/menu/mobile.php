<?php

/* this is auto generated file */
return [
    [
        'menu'     => 'notification.mobile_menu_more',
        'name'     => 'mark_all_as_read',
        'label'    => 'notification::phrase.mark_all_as_read',
        'ordering' => 1,
        'value'    => '@notification/markAllRead',
    ],
    [
        'style'    => 'danger',
        'menu'     => 'notification.mobile_menu_more',
        'name'     => 'delete_all_notification',
        'label'    => 'notification::phrase.delete_all_notifications',
        'ordering' => 2,
        'value'    => '@notification/deleteAllNotifications',
    ],
    [
        'menu'     => 'notification.notification.detailActionMenu',
        'name'     => 'mark_read',
        'label'    => 'notification::phrase.mark_as_read',
        'ordering' => 1,
        'value'    => 'notification/markAsRead',
    ],
    [
        'className' => 'itemDelete',
        'menu'      => 'notification.notification.detailActionMenu',
        'name'      => 'delete',
        'label'     => 'notification::phrase.delete',
        'ordering'  => 2,
        'value'     => 'notification/deleteItem',
    ],
    [
        'menu'     => 'notification.notification.itemActionMenu',
        'name'     => 'mark_read',
        'label'    => 'notification::phrase.mark_as_read',
        'ordering' => 1,
        'value'    => 'notification/markAsRead',
    ],
    [
        'className' => 'itemDelete',
        'style'     => 'danger',
        'menu'      => 'notification.notification.itemActionMenu',
        'name'      => 'delete',
        'label'     => 'notification::phrase.delete',
        'ordering'  => 2,
        'value'     => 'notification/deleteItem',
    ],
];
