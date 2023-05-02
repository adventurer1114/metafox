<?php

use Carbon\Carbon;

$siteName  = config('app.name');
$siteTitle = config('app.site_title');

return [
    'homepage_url' => [
        'value'     => '',
        'type'      => 'string',
        'is_public' => 1,
    ],
    'end_head_html' => [
        'value'     => '',
        'type'      => 'string',
        'is_public' => 0,
    ],
    'start_body_html' => [
        'value'     => '',
        'type'      => 'string',
        'is_public' => 0,
    ],
    'end_body_html' => [
        'value'     => '',
        'type'      => 'string',
        'is_public' => 0,
    ],
    'offline' => [
        // @deprecated: used base on file_exists storage/framework/down
        'value' => 0,
        'type'  => 'integer',
    ],
    'offline_message' => [
        'value' => '<h2>Website is currently down for maintenance.</h2>',
        'type'  => 'string',
    ],
    'license' => [
        'is_public' => 0,
        'value'     => [
            'id'  => config('app.mfox_license_id'),
            'key' => config('app.mfox_license_key'),
        ],
    ],
    'app.debug' => [
        'env'         => 'APP_DEBUG',
        'value'       => false,
        'config_name' => null,
    ],
    'platform.installed_at' => [
        'is_public' => 0,
        'value'     => '',
        'type'      => 'string',
    ],
    'platform.upgraded_at' => [
        'is_public' => 0,
        'value'     => '',
        'type'      => 'string',
    ],
    'platform.expired_at' => [
        'is_public' => 0,
        'value'     => '',
        'type'      => 'string',
    ],
    'platform.latest_version' => [
        'is_public' => 0,
        'value'     => '',
        'type'      => 'string',
    ],
    'setting_version_id' => [
        'value' => time(),
    ],
    'setting_updated_at' => [
        'is_public' => 0,
        'value'     => Carbon::now()->format('Y-m-d H:i:s'),
    ],
    'general.site_name' => [
        'config_name' => 'app.name',
        'value'       => $siteName,
    ],
    'general.site_title' => [
        'env_var' => 'MFOX_SITE_TITLE', 'value' => $siteTitle,
    ],
    'general.start_of_week' => [
        'env_var' => 'MFOX_START_OF_WEEK',
        'value'   => Carbon::MONDAY,
    ],
    'general.title_delim' => [
        'env_var' => 'MFOX_SITE_TITLE_DELIM',
        'value'   => '•',
    ],
    'general.title_append' => [
        'value' => 1,
    ],
    'general.site_copyright' => [
        'env_var' => 'MFOX_SITE_COPYRIGHT',
        'value'   => "$siteName ©",
    ],
    'general.site_offline' => [
        'env_var' => 'MFOX_SITE_OFFLINE',
        'value'   => 0,
    ],
    'general.keywords' => [
        'env_var' => 'MFOX_SITE_KEYWORDS',
        'value'   => $siteTitle,
    ],
    'general.description' => [
        'env_var' => 'MFOX_SITE_DESCRIPTION',
        'value'   => $siteTitle,
    ],
    'general.enable_2step_verification' => [
        'value' => false,
    ],
    'general.gdpr_enabled' => [
        'value' => false,
    ],
    'general.friends_only_community' => [
        'value' => false,
    ],
    'general.min_character_to_search' => [
        'value' => 2,
    ],
    'general.no_pages_for_scroll_down' => [
        'value' => 2,
    ],
    'general.site_stat_entities' => [
        'value'     => [],
        'type'      => 'array',
        'is_public' => 0,
    ],
    'general.site_stat_icons' => [
        'value'     => [],
        'type'      => 'array',
        'is_public' => 0,
    ],
    'cookie.path' => [
        'is_public'   => 0,
        'env_var'     => 'MFOX_COOKIE_PATH',
        'config_name' => 'session.cookie_path',
        'value'       => '/',
    ],
    'cookie.domain' => [
        'is_public' => 0,
        'env_var'   => 'MFOX_COOKIE_DOMAIN',
        'value'     => '',
    ],
    'cookie.prefix' => [
        'is_public'   => 0,
        'env_var'     => 'MFOX_COOKIE_PREFIX',
        'config_name' => 'session.cookie_prefix',
        'value'       => 'yA0JuFD6n6zkC1',
    ],

    'attachment.maximum_number_of_attachments_that_can_be_uploaded' => [
        'value' => 5,
    ],
    'attachment.maximum_file_size_each_attachment_can_be_uploaded' => [
        'value' => 8192, // 8MB
    ],
    'spam.warning_on_external_links' => [
        'value' => false,
    ],
    'google.google_map_api_key' => [
        'env_var' => 'MFOX_GOOGLE_MAP_API_KEY',
        'value'   => '',
    ],
    'services.ses' => [
        'is_public' => 0,
        'value'     => [],
    ],
    'services.mailgun' => [
        'is_public' => 0,
        'value'     => [],
    ],
    'services.postmark' => [
        'is_public' => 0,
        'value'     => [],
    ],
    'services.google' => [
        'is_public' => 0,
        'value'     => [],
    ],
    'services.facebook' => [
        'is_public' => 0,
        'value'     => [],
    ],
    'services.twitter' => [
        'is_public' => 0,
        'value'     => [],
    ],
    'services.apple' => [
        'is_public' => 0,
        'value'     => [],
    ],
    'base_path' => [
        'is_deleted' => 1, // remove this issues.
        'is_public'  => 0,
        'type'       => 'string',
        'value'      => base_path(),
    ],
];
