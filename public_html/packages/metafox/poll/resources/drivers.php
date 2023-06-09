<?php

/* this is auto generated file */
return [
    [
        'driver'     => 'MetaFox\\Poll\\Models\\Poll',
        'type'       => 'entity',
        'name'       => 'poll',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Polls',
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Models\\Answer',
        'type'       => 'entity',
        'name'       => 'poll_answer',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Models\\Design',
        'type'       => 'entity',
        'name'       => 'poll_design',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Models\\Result',
        'type'       => 'entity',
        'name'       => 'poll_result',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Models\\Poll',
        'type'       => 'entity-content',
        'name'       => 'poll',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'Polls',
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\ActivityPollForm',
        'type'       => 'form',
        'name'       => 'poll.activity',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\StatusCreatePollMobileForm',
        'type'       => 'form',
        'name'       => 'poll.feed_form',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\StatusCreatePollForm',
        'type'       => 'form',
        'name'       => 'poll.feed_form',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\IntegrationCreatePollForm',
        'type'       => 'form',
        'name'       => 'poll.integration_create',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\MobileIntegrationCreatePollForm',
        'type'       => 'form',
        'name'       => 'poll.integration_poll_mobile',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\StorePollMobileForm',
        'type'       => 'form',
        'name'       => 'poll.poll.store',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\UpdatePollMobileForm',
        'type'       => 'form',
        'name'       => 'poll.poll.update',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\SearchPollMobileForm',
        'type'       => 'form',
        'name'       => 'poll.search',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => true,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\SearchPollForm',
        'type'       => 'form',
        'name'       => 'poll.search',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => true,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\Admin\\SiteSettingForm',
        'type'       => 'form',
        'name'       => 'poll.site_setting',
        'version'    => 'v1',
        'resolution' => 'admin',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\StorePollForm',
        'type'       => 'form',
        'name'       => 'poll.store',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\StorePollMobileForm',
        'type'       => 'form',
        'name'       => 'poll.store_poll_mobile',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\UpdatePollForm',
        'type'       => 'form',
        'name'       => 'poll.update',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\UpdatePollMobileForm',
        'type'       => 'form',
        'name'       => 'poll.update_poll_mobile',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Support\\Form\\Field\\AttachPoll',
        'type'       => 'form-field',
        'name'       => 'attachPoll',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Form\\Mobile\\PollAnswerField',
        'type'       => 'form-field',
        'name'       => 'pollAnswer',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Form\\Html\\PollAnswer',
        'type'       => 'form-field',
        'name'       => 'pollAnswer',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Form\\Html\\PollCloseTime',
        'type'       => 'form-field',
        'name'       => 'pollCloseTime',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Form\\Mobile\\PollCloseTimeField',
        'type'       => 'form-field',
        'name'       => 'pollCloseTime',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\Admin\\SiteSettingForm',
        'type'       => 'form-settings',
        'name'       => 'poll',
        'version'    => 'v1',
        'resolution' => 'admin',
        'is_active'  => true,
        'is_preload' => false,
        'title'      => 'core::phrase.settings',
        'url'        => '/admincp/poll/setting',
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Answer\\AnswerItemCollection',
        'type'       => 'json-collection',
        'name'       => 'poll_answer.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\PollEmbedCollection',
        'type'       => 'json-collection',
        'name'       => 'poll.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\PollItemCollection',
        'type'       => 'json-collection',
        'name'       => 'poll.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Result\\ResultItemCollection',
        'type'       => 'json-collection',
        'name'       => 'poll_result.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Answer\\AnswerItem',
        'type'       => 'json-resource',
        'name'       => 'poll_answer.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Design\\DesignDetail',
        'type'       => 'json-resource',
        'name'       => 'poll_design.detail',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\PollDetail',
        'type'       => 'json-resource',
        'name'       => 'poll.detail',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\PollEmbed',
        'type'       => 'json-resource',
        'name'       => 'poll.embed',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\PollItem',
        'type'       => 'json-resource',
        'name'       => 'poll.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Result\\ResultItem',
        'type'       => 'json-resource',
        'name'       => 'poll_result.item',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\PackageSetting',
        'type'       => 'package-setting',
        'name'       => 'poll',
        'version'    => 'v1',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Policies\\PollPolicy',
        'type'       => 'policy-resource',
        'name'       => 'MetaFox\\Poll\\Models\\Poll',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'poll',
        'version'    => 'v1',
        'resolution' => 'mobile',
        'is_active'  => true,
        'is_preload' => false,
    ],
    [
        'driver'     => 'MetaFox\\Poll\\Http\\Resources\\v1\\Poll\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'poll',
        'version'    => 'v1',
        'resolution' => 'web',
        'is_active'  => true,
        'is_preload' => false,
    ],
];
