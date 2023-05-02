<?php

 /* this is auto generated file */
 return [
     [
         'menu'        => 'core.adminSidebarMenu',
         'name'        => 'health-check',
         'parent_name' => 'app-settings',
         'is_deleted'  => true,
     ],
     [
         'menu'        => 'core.adminSidebarMenu',
         'name'        => 'health-check',
         'parent_name' => 'maintenance',
         'label'       => 'health-check::phrase.health_check',
         'to'          => '/admincp/health-check/wizard',
         'ordering'    => 5,
     ],
     [
         'menu'     => 'health-check.admin',
         'name'     => 'check',
         'label'    => 'health-check::phrase.health_check',
         'to'       => '/admincp/health-check/wizard',
         'ordering' => 5,
     ],
 ];
