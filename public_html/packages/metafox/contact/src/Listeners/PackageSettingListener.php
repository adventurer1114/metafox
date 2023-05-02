<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Contact\Listeners;

use MetaFox\Platform\Support\BasePackageSettingListener;

/**
 * --------------------------------------------------------------------------
 * Code Generator
 * --------------------------------------------------------------------------
 * stub: src/Listeners/PackageSettingListener.stub.
 */

/**
 * Class PackageSettingListener.
 * @SuppressWarnings(PHPMD)
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    /**
     * @return string[]
     */
    public function getCaptchaRules(): array
    {
        return [
            'contact',
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'staff_emails'            => ['value' => ''],
            'enable_auto_responder'   => ['value' => 1],
            'allow_html_contact_form' => ['value' => 1],
            'default_category'        => ['value' => 1],
        ];
    }
}
