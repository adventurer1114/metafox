<?php

 /* this is auto generated file */
 return [
     [
         'subInfo'   => '',
         'menu'      => 'core.primaryMenu',
         'name'      => 'advertises',
         'label'     => 'advertise::phrase.advertise',
         'ordering'  => 2,
         'icon'      => 'ico-speaker',
         'to'        => '/advertise',
         'is_active' => 0,

     ],
     [
         'tab'      => 'my',
         'menu'     => 'advertise.sidebarMenu',
         'name'     => 'my',
         'label'    => 'advertise::phrase.all_ads',
         'ordering' => 1,
         'icon'     => 'ico-speaker',
         'to'       => '/advertise',
         'showWhen' => [
             'and',
             ['truthy', 'session.loggedIn'],
         ],
     ],
     [
         'tab'      => 'invoice',
         'menu'     => 'advertise.sidebarMenu',
         'name'     => 'invoice',
         'label'    => 'advertise::phrase.invoices',
         'ordering' => 2,
         'icon'     => 'ico-merge-file-o',
         'to'       => '/advertise/invoice',
         'showWhen' => [
             'and',
             ['truthy', 'session.loggedIn'],
         ],
     ],
     [
         'showWhen' => [
             'and',
             ['truthy', 'session.loggedIn'],
             ['truthy', 'acl.advertise.advertise.create'],
         ],
         'buttonProps' => [
             'fullWidth' => true,
             'color'     => 'primary',
             'variant'   => 'contained',
         ],
         'menu'     => 'advertise.sidebarMenu',
         'name'     => 'add',
         'label'    => 'advertise::phrase.create_new_ad',
         'ordering' => 4,
         'as'       => 'sidebarButton',
         'icon'     => 'ico-plus',
         'to'       => '/advertise/add',
     ],
     [
         'showWhen' => [
             'and',
             ['truthy', 'item.extra.can_payment'],
         ],
         'menu'     => 'advertise.advertise.itemActionMenu',
         'name'     => 'payment',
         'label'    => 'pay_price',
         'ordering' => 1,
         'value'    => 'advertise/paymentItem',
         'icon'     => 'ico-credit-card-o',
     ],
     [
         'showWhen' => [
             'and',
             ['truthy', 'item.extra.can_edit'],
         ],
         'menu'     => 'advertise.advertise.itemActionMenu',
         'name'     => 'edit',
         'label'    => 'core::phrase.edit',
         'ordering' => 2,
         'value'    => 'editItem',
         'icon'     => 'ico-pencilline-o',
     ],
     [
         'showWhen' => [
             'and',
             ['truthy', 'item.extra.can_delete'],
         ],
         'className' => 'itemDelete',
         'menu'      => 'advertise.advertise.itemActionMenu',
         'name'      => 'delete',
         'label'     => 'core::phrase.delete',
         'ordering'  => 3,
         'value'     => 'deleteItem',
         'icon'      => 'ico-trash',
     ],
     [
         'showWhen' => [
             'and',
             ['truthy', 'item.extra.can_payment'],
         ],
         'menu'     => 'advertise.advertise.detailActionMenu',
         'name'     => 'payment',
         'label'    => 'pay_price',
         'ordering' => 1,
         'value'    => 'advertise/paymentItem',
         'icon'     => 'ico-credit-card-o',
         'variant'  => 'contained',
         'color'    => 'primary',
     ],
     [
         'showWhen' => [
             'and',
             ['truthy', 'item.extra.can_edit'],
         ],
         'menu'     => 'advertise.advertise.detailActionMenu',
         'name'     => 'edit',
         'label'    => 'core::phrase.edit',
         'ordering' => 2,
         'value'    => 'editItem',
         'icon'     => 'ico-pencilline-o',
         'variant'  => 'outlined',
         'color'    => 'primary',
     ],
     [
         'showWhen' => [
             'and',
             ['truthy', 'item.extra.can_delete'],
         ],
         'className' => 'itemDelete',
         'menu'      => 'advertise.advertise.detailActionMenu',
         'name'      => 'delete',
         'label'     => 'core::phrase.delete',
         'ordering'  => 3,
         'value'     => 'deleteItem',
         'icon'      => 'ico-trash',
         'variant'   => 'outlined',
         'color'     => 'error',
     ],
     [
         'showWhen' => [
             'and',
             ['truthy', 'item.extra.can_payment'],
         ],
         'menu'     => 'advertise.advertise_invoice.itemActionMenu',
         'name'     => 'payment',
         'label'    => 'advertise::web.pay_now',
         'ordering' => 1,
         'value'    => 'advertise/paymentItem',
         'icon'     => 'ico-credit-card-o',
     ],
     [
         'showWhen' => [
             'and',
             ['truthy', 'item.extra.can_cancel'],
         ],
         'menu'     => 'advertise.advertise_invoice.itemActionMenu',
         'name'     => 'cancel',
         'label'    => 'core::phrase.cancel',
         'ordering' => 2,
         'value'    => 'advertise/cancelItem',
         'icon'     => 'ico-close-circle-o',
     ],
     [
         'subInfo'  => 'advertise::phrase.dropdown_menu_description',
         'menu'     => 'core.dropdownMenu',
         'name'     => 'advertise',
         'label'    => 'advertise::phrase.advertise',
         'ordering' => 4,
         'icon'     => 'ico-speaker',
         'to'       => '/advertise',
     ],
     [
         'menu'     => 'core.leftFooterMenu',
         'name'     => 'advertise',
         'label'    => 'advertise::phrase.advertise',
         'ordering' => 1,
         'to'       => '/advertise',
     ],
 ];
