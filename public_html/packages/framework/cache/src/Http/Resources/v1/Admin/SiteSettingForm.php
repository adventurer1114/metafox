<?php

namespace MetaFox\Cache\Http\Resources\v1\Admin;

use Illuminate\Support\Arr;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Yup\Yup;

/**
 * | --------------------------------------------------------------------------
 * | Form Configuration
 * | --------------------------------------------------------------------------
 * | stub: src/Http/Resources/v1/Admin/SiteSettingForm.stub.
 */

/**
 * Class SiteSettingForm.
 * @codeCoverageIgnore
 * @ignore
 */
class SiteSettingForm extends AbstractForm
{
    private array $variables = [];

    private bool $disabled = false;

    /**
     * @return array
     */
    public function getCacheOptions(): array
    {
        $cacheOptions = [];

        $stores = config('cache.stores', []);

        foreach ($stores as $key => $value) {
            if ($value['selectable'] ?? false) {
                $cacheOptions[] = [
                    'label' => $value['label'] ?? ucfirst($key),
                    'value' => $key,
                ];
            }
        }

        return $cacheOptions;
    }

    protected function prepare(): void
    {
        $vars = [
            'cache.default',
            'cache.prefix',
        ];

        $values = [];

        foreach ($vars as $var) {
            Arr::set($values, $var, Settings::get($var));
        }

        $this->title(__p('core::phrase.settings'))
            ->action('admincp/setting/cache')
            ->asPost()
            ->setValue($values);
    }

    protected function initialize(): void
    {
        $cacheOptions = $this->getCacheOptions();

        $this->addBasic()
            ->addFields(
                Builder::choice('cache.default')
                    ->label(__p('cache::phrase.default_cache_label'))
                    ->description(__p('cache::phrase.default_cache_desc'))
                    ->required()
                    ->options($cacheOptions),
                Builder::text('cache.prefix')
                    ->label(__p('cache::phrase.cache_key_prefix_label'))
                    ->description(__p('cache::phrase.cache_key_prefix_desc'))
                    ->yup(Yup::string()->nullable()->matches('\w+')),
            );

        $this->addDefaultFooter(true);
    }
}
