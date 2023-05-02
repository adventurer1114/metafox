<?php

namespace MetaFox\Core\Http\Resources\v1\Admin;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use MetaFox\Core\Models\SiteSetting as Model;
use MetaFox\Form\AdminSettingForm as Form;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class GeneralSiteSettingForm.
 * @property Model $resource
 */
class SiteSettingForm extends Form
{
    protected function prepare(): void
    {
        $vars = [
            'core.homepage_url',
            'core.end_head_html',
            'core.start_body_html',
            'core.end_body_html',
            'core.general.site_name',
            'core.general.site_title',
            'core.general.no_pages_for_scroll_down',
            'core.general.site_copyright',
            'core.general.title_delim',
            'core.general.title_append',
            'core.general.keywords',
            'core.general.description',
            'core.general.gdpr_enabled',
            'core.general.enable_2step_verification',
            'core.general.friends_only_community',
            'core.general.min_character_to_search',
            'core.general.start_of_week',
            'core.google.google_map_api_key',
        ];

        $value = [];

        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get($var));
        }

        $this->asPost()
            ->title(__p('core::phrase.site_settings'))
            ->action('admincp/setting/core')
            ->setValue($value);
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::text('core.general.site_name')
                ->label(__p('core::admin.name_of_site_label'))
                ->description(__p('core::admin.name_of_site_desc')),
            Builder::text('core.general.site_title')
                ->label(__p('core::admin.site_title_label'))
                ->description(__p('core::admin.site_title_desc')),
            Builder::dropdown('core.general.start_of_week')
                ->label(__p('core::phrase.week_starts_on'))
                ->options($this->getDayOptions()),
            Builder::text('core.general.site_copyright')
                ->required()
                ->label(__p('core::admin.copyright_label'))
                ->description(__p('core::admin.copyright_desc')),
            Builder::text('core.general.title_delim')
                ->required()
                ->label(__p('core::admin.site_title_delimiter_label'))
                ->description(__p('core::admin.site_title_delimiter_desc')),
            Builder::textArea('core.general.keywords')
                ->required()
                ->variant('outlined')
                ->label(__p('core::admin.meta_keywords_label'))
                ->description(__p('core::admin.meta_keywords_desc')),
            Builder::textArea('core.general.description')
                ->required()
                ->variant('outlined')
                ->label(__p('core::admin.meta_description_label'))
                ->description(__p('core::admin.meta_description_desc')),
            Builder::text('core.homepage_url')
                ->label(__p('core::admin.homepage_url_label'))
                ->description(__p('core::admin.homepage_url_description')),
            Builder::textArea('core.end_head_html')
                ->optional()
                ->variant('outlined')
                ->label(__p('core::admin.append_head_scripts_label'))
                ->description(__p('core::admin.append_head_scripts_desc')),
            Builder::textArea('core.start_body_html')
                ->optional()
                ->variant('outlined')
                ->label(__p('core::admin.prepend_body_scripts_label'))
                ->description(__p('core::admin.prepend_body_scripts_desc')),
            Builder::textArea('core.end_body_html')
                ->optional()
                ->variant('outlined')
                ->label(__p('core::admin.append_body_scripts_label'))
                ->description(__p('core::admin.append_body_scripts_desc')),
            Builder::switch('core.general.gdpr_enabled')
                ->label(__p('core::admin.enable_general_data_protection_regulation_label'))
                ->description(__p('core::admin.enable_general_data_protection_regulation_desc')),
            Builder::text('core.general.no_pages_for_scroll_down')
                ->required()
                ->variant('outlined')
                ->label(__p('core::admin.number_of_page_for_scrolling_down_label'))
                ->placeholder('2')
                ->description(__p('core::admin.number_of_page_for_scrolling_down_desc')),

            /* TODO: unhide when implementing
             * Builder::switch('core.general.friends_only_community')
                ->required()
                ->variant('outlined')
                ->label('Friends Only Community')
                ->description('By enabling this option certain sections (eg. Blogs, Photos etc...), will by default only show items from the member and his or her friends list.'),*/

            Builder::text('core.general.min_character_to_search')
                ->required()
                ->variant('outlined')
                ->label(__p('core::admin.global_search_minimum_character_label'))
                ->description(__p('core::admin.global_search_minimum_character_desc'))
                ->yup(Yup::number()->int()->min(1)),
            Builder::text('core.google.google_map_api_key')
                ->label(__p('core::admin.google_map_api_key'))
                ->description(__p('core::admin.google_map_api_key_description'))
                ->optional(),
        );

        $this->addDefaultFooter(true);
    }

    /**
     * getDayOptions.
     *
     * @return array<mixed>
     */
    protected function getDayOptions(): array
    {
        return [
            [
                'label' => __p('core::phrase.monday'),
                'value' => Carbon::MONDAY,
            ],
            [
                'label' => __p('core::phrase.tuesday'),
                'value' => Carbon::TUESDAY,
            ],
            [
                'label' => __p('core::phrase.wednesday'),
                'value' => Carbon::WEDNESDAY,
            ],
            [
                'label' => __p('core::phrase.thursday'),
                'value' => Carbon::THURSDAY,
            ],
            [
                'label' => __p('core::phrase.friday'),
                'value' => Carbon::FRIDAY,
            ],
            [
                'label' => __p('core::phrase.saturday'),
                'value' => Carbon::SATURDAY,
            ],
            [
                'label' => __p('core::phrase.sunday'),
                'value' => Carbon::SUNDAY,
            ],
        ];
    }
}
