<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\Admin;

use Illuminate\Support\Arr;
use MetaFox\Form\AdminSettingForm as Form;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;

class SiteSettingForm extends Form
{
    protected function prepare(): void
    {
        $module = 'activitypoint';
        $vars   = [
            'activitypoint.conversion_rate',
        ];

        $value = [];

        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get($var));
        }

        $this->title(__p('core::phrase.settings'))
            ->action(url_utility()->makeApiUrl('admincp/setting/' . $module))
            ->asPost()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::currencyPricingGroup('activitypoint.conversion_rate')
                ->buildFields()
                ->label(__p('activitypoint::phrase.conversation_rate_label'))
                ->description(__p('activitypoint::phrase.conversation_rate_desc'))
        );

        $this->addDefaultFooter(true);
    }
}
