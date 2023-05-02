<?php

 /* this is auto generated file */
 return [
     [
         'menu'     => 'advertise.admin',
         'name'     => 'settings',
         'label'    => 'core::phrase.settings',
         'ordering' => 1,
         'to'       => '/admincp/advertise/setting',
     ],
     [
         'menu'     => 'advertise.admin',
         'name'     => 'permissions',
         'label'    => 'core::phrase.permissions',
         'ordering' => 2,
         'to'       => '/admincp/advertise/permission',
     ],
     [
         'menu'     => 'advertise.admin',
         'name'     => 'manage_placements',
         'label'    => 'advertise::phrase.manage_placements',
         'ordering' => 3,
         'to'       => '/admincp/advertise/placement/browse',
     ],
     [
         'menu'     => 'advertise.admin',
         'name'     => 'add_placement',
         'label'    => 'advertise::phrase.create_new_placement',
         'ordering' => 4,
         'to'       => '/admincp/advertise/placement/create',
     ],
     [
         'menu'     => 'advertise.admin',
         'name'     => 'manage_advertises',
         'label'    => 'advertise::phrase.manage_advertises',
         'ordering' => 5,
         'to'       => '/admincp/advertise/advertise/browse',
     ],
     [
         'menu'     => 'advertise.admin',
         'name'     => 'add_advertise',
         'label'    => 'advertise::phrase.create_new_advertise',
         'ordering' => 6,
         'to'       => '/admincp/advertise/advertise/create',
     ],
     [
         'menu'     => 'advertise.admin',
         'name'     => 'manage_invoice',
         'label'    => 'advertise::phrase.manage_invoices',
         'ordering' => 7,
         'to'       => '/admincp/advertise/invoice/browse',
     ],
     [
         'menu'        => 'core.adminSidebarMenu',
         'parent_name' => 'app-settings',
         'name'        => 'advertise',
         'label'       => 'advertise::phrase.advertise',
         'ordering'    => 1,
         'to'          => '/admincp/advertise/setting',
     ],
 ];
