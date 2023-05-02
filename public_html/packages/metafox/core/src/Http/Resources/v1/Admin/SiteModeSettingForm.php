<?php

namespace MetaFox\Core\Http\Resources\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use MetaFox\Form\AdminSettingForm as Form;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Yup\Yup;

/**
 * Class SiteModeSettingForm.
 */
class SiteModeSettingForm extends Form
{
    protected function prepare(): void
    {
        $vars = [
            'core.offline_message',
        ];

        $value = [];

        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get($var));
        }

        Arr::set($value, 'core.offline', file_exists(base_path('storage/framework/down')));

        $this->title(__p('core::phrase.site_settings'))
            ->action('admincp/setting/core/site-mode')
            ->asPost()
            ->setValue($value);
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::checkbox('core.offline')
                ->required()
                ->variant('outlined')
                ->marginNormal()
                ->label(__p('core::phrase.site_is_offline'))
                ->yup(
                    Yup::string()
                ),
            Builder::textArea('core.offline_message')
                ->variant('outlined')
                ->marginNormal()
                ->label(__p('core::phrase.offline_message')),
        );

        $this->addDefaultFooter(true);
    }

    public function validated(Request $request): array
    {
        $params = $request->validate([
            'core.offline'         => 'sometimes|boolean',
            'core.offline_message' => 'string|sometimes',
        ]);

        $offline  = Arr::get($params, 'core.offline');
        $modified = $offline != file_exists(base_path('storage/framework/down'));

        if ($modified) {
            if ($offline) {
                Artisan::call('down');
            } else {
                Artisan::call('up');
            }
            Artisan::call('cache:reset');
        }

        return $params;
    }
}
