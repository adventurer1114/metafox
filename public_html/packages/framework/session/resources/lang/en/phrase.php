<?php

/* this is auto generated file */
return [
    'connection_desc'        => 'When using the "database" or "redis" session drivers, you may specify a connection that should be used to manage these sessions. This should correspond to a connection in your database configuration options.',
    'connection_label'       => 'Session Database Connection',
    'cookie_desc'            => 'Here you may change the name of the cookie used to identify a session instance by ID. The name specified here will get used every time a new session cookie is created by the framework for every driver.',
    'cookie_label'           => 'Session Cookie Name',
    'domain_desc'            => 'Here you may change the domain of the cookie used to identify a session in your application. This will determine which domains the cookie is available to in your application. A sensible default has been set.',
    'domain_label'           => 'Session Cookie Domain',
    'driver_desc'            => 'This option controls the default session "driver" that will be used on requests. By default, we will use the lightweight native driver but you may specify any of the other wonderful drivers provided here.',
    'driver_label'           => 'Default Session Driver',
    'encrypt_desc'           => 'Encrypt all session data to be store.',
    'encrypt_label'          => 'Encrypt session data',
    'expire_on_close_label'  => 'Expire on close',
    'files_desc'             => 'When using the native session driver, we need a location where session files may be stored. A default has been set for you but a different location may be specified. This is only needed for file sessions. defaults: ./storage/framework/sessions',
    'files_label'            => 'Session File Location',
    'guide_driver_apc'       => 'Sessions are stored in local APC.',
    'guide_driver_array'     => 'Sessions are stored in a PHP array and will not be persisted.',
    'guide_driver_cookie'    => 'Sessions are stored in secure, encrypted cookies.',
    'guide_driver_database'  => 'Sessions are stored in a relational database.',
    'guide_driver_dynamodb'  => 'Sessions are stored in AWS DynamoDB.',
    'guide_driver_file'      => 'Sessions are stored in filesystem.',
    'guide_driver_memcache'  => 'Sessions are stored in memcached store.',
    'guide_driver_redis'     => 'Sessions are stored in Redis cache store.',
    'guide_same_site_lax'    => 'Cookies are not sent on normal cross-site subrequests (for example to load images or frames into a third party site), but are sent when a user is navigating to the origin site (i.e., when following a link)',
    'guide_same_site_none'   => 'Cookies will be sent in all contexts, i.e. in responses to both first-party and cross-site requests. If SameSite=None is set, the cookie Secure attribute must also be set (or the cookie will be blocked).',
    'guide_same_site_strict' => 'Cookies will only be sent in a first-party context and not be sent along with requests initiated by third party websites',
    'http_only_desc'         => 'Setting this value to true will prevent JavaScript from accessing the value of the cookie and the cookie will only be accessible through the HTTP protocol. You are free to modify this option if needed',
    'http_only_label'        => 'HTTP Access Only',
    'lifetime_desc'          => 'Here you may specify the number of minutes that you wish the session to be allowed to remain idle before it expires. If you want them to immediately expire on the browser closing, set that option.',
    'lifetime_label'         => 'Session Lifetime',
    'path_desc'              => 'The session cookie path determines the path for which the cookie will be regarded as available. Typically, this will be the root path of your application but you are free to change this when necessary.',
    'path_label'             => 'Session Cookie Path',
    'same_site_desc'         => 'This option determines how your cookies behave when cross-site requests take place, and can be used to mitigate CSRF attacks. By default, we will set this value to "lax" since this is a secure default value. Supported: "lax", "strict", "none", null',
    'same_site_label'        => 'Same-Site Cookies',
    'secure_desc'            => 'By setting this option to true, session cookies will only be sent back to the server if the browser has a HTTPS connection. This will keep the cookie from being sent to you when it cannot be done securely.',
    'secure_label'           => 'HTTPS Only Cookies',
    'session'                => 'Session',
    'session_stores'         => 'Session Stores',
    'settings'               => 'Settings',
    'site_settings'          => 'Session Settings',
    'store_desc'             => 'While using one of the framework\'s cache driven session backends you may list a cache store that should be used for these sessions. This value must match with one of the application\'s configured cache "stores".
     Affects: "apc", "dynamodb", "memcached", "redis"',
    'store_label' => 'Session Cache Store',
    'table_desc'  => 'When using the "database" session driver, you may specify the table we should use to manage the sessions. Of course, a sensible default is provided for you; however, you are free to change this as needed. default: sessions',
    'table_label' => 'Session Database Table',
];
