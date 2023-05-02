<?php

namespace MetaFox\Advertise\Http\Resources\v1\Admin;

use Illuminate\Support\Arr;
use MetaFox\Form\Builder;
use MetaFox\Form\AbstractForm;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Yup\Yup;

/**
 | --------------------------------------------------------------------------
 | Form Configuration
 | --------------------------------------------------------------------------
 | stub: src/Http/Resources/v1/Admin/SiteSettingForm.stub
 */

/**
 * Class SiteSettingForm.
 * @codeCoverageIgnore
 * @ignore
 */
class SiteSettingForm extends AbstractForm
{
    protected function prepare(): void
    {
        $module = 'advertise';

        $vars   = [
            'enable_advertise',
            'enable_advanced_filter',
            'show_create_button_on_block',
            'collapse_on_480',
            'collapse_on_767',
            'collapse_on_992',
            'maximum_number_of_advertises_on_side_block',
        ];

        $value = [];

        foreach ($vars as $var) {
            $var = $module . '.' . $var;
            Arr::set($value, $var, Settings::get($var));
        }

        $this->title(__p('core::phrase.settings'))
            ->action('admincp/setting/' . $module)
            ->asPost()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::switch('advertise.enable_advertise')
                    ->label(__p('advertise::admin.enable_advertises_label'))
                    ->description(__p('advertise::admin.enable_advertises_desc')),
                Builder::switch('advertise.enable_advanced_filter')
                    ->label(__p('advertise::admin.enable_advanced_filter_label'))
                    ->description(__p('advertise::admin.enable_advanced_filter_desc')),
                /*Builder::switch('advertise.show_create_button_on_block')
                    ->label(__p('advertise::admin.show_create_button_on_block_label'))
                    ->description(__p('advertise::admin.show_create_button_on_block_desc')),*/
                Builder::text('advertise.maximum_number_of_advertises_on_side_block')
                    ->label(__p('advertise::admin.maximum_number_of_advertises_on_side_block_label'))
                    ->description(__p('advertise::admin.maximum_number_of_advertises_on_side_block_desc'))
                    ->yup(
                        Yup::number()
                            ->required()
                            ->min(1)
                    ),
            );

        $this->addDefaultFooter(true);
    }
}
