<?php

 /* this is auto generated file */
 return [
     [
         'menu'        => 'core.adminSidebarMenu',
         'name'        => 'mobile',
         'parent_name' => 'app-settings',
         'label'       => 'Mobile Services',
         'testid'      => 'mobile',
         'to'          => '/admincp/mobile/setting',
     ],
     [
         'menu'     => 'mobile.admin',
         'name'     => 'settings',
         'label'    => 'core::phrase.settings',
         'to'       => '/admincp/mobile/setting',
         'ordering' => 1,
     ],
     [
         'menu'     => 'mobile.admin',
         'name'     => 'admob_config',
         'label'    => 'mobile::phrase.manage_ad_config',
         'to'       => '/admincp/mobile/admob/browse',
         'ordering' => 2,
     ],
     [
         'menu'     => 'mobile.admin',
         'name'     => 'add_admob_config',
         'label'    => 'mobile::phrase.add_ad_config',
         'to'       => '/admincp/mobile/admob/create',
         'ordering' => 3,
     ],
 ];
