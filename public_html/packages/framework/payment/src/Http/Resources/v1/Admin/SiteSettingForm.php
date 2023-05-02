<?php

namespace MetaFox\Payment\Http\Resources\v1\Admin;

use Illuminate\Support\Arr;
use MetaFox\Form\AdminSettingForm as Form;
use MetaFox\Platform\Facades\Settings;

/**
 * | --------------------------------------------------------------------------
 * | Form Configuration
 * | --------------------------------------------------------------------------
 * | stub: src/Http/Resources/v1/Admin/SiteSettingForm.stub.
 */

/**
 * Class SiteSettingForm.
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class SiteSettingForm extends Form
{
    protected function prepare(): void
    {
        $module = 'payment';
        $vars   = [

        ];

        $value = [];

        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get($var));
        }

        $this
            ->title(__p('core::phrase.settings'))
            ->action(url_utility()->makeApiUrl('admincp/setting/' . $module))
            ->asPost()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $this->addDefaultFooter();
    }
}