<?php

namespace MetaFox\Localize\Http\Resources\v1\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Localize\Models\Language;
use MetaFox\Localize\Repositories\Eloquent\LanguageRepository;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Rules\AllowInRule;

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
    protected function prepare(): void
    {
        $module = 'localize';

        $vars   = [
            'localize.disable_translation',
            'localize.default_locale',
        ];

        $value = [];

        foreach ($vars as $var) {
            Arr::set($value, $var, Settings::get($var));
        }

        $this->title(__p('localize::phrase.localize_settings'))
            ->action('admincp/setting/' . $module)
            ->asPost()
            ->setValue($value);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::switch('localize.disable_translation')
                    ->label(__p('localize::phrase.active_translation_label'))
                    ->description(__p('localize::phrase.disable_translation_desc')),
                Builder::choice('localize.default_locale')
                    ->label(__p('localize::phrase.default_locale_label'))
                    ->description(__p('localize::phrase.default_locale_desc'))
                    ->options($this->getLocaleOptions()),
            );

        $this->addDefaultFooter(true);
    }

    private function getLocaleOptions(): array
    {
        return resolve(LanguageRepository::class)->getModel()
            ->newQuery()
            ->where('is_active', 1)
            ->get(['name', 'language_code'])
            ->map(function (Language $locale) {
                return ['label' => $locale->name, 'value' => $locale->language_code];
            })
            ->toArray();
    }

    public function validated(Request $request): array
    {
        $rules = [
            'localize.disable_translation' => ['boolean', new AllowInRule([true, false])],
            'localize.default_locale'      => ['string', 'exists:core_languages,language_code'],
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules, [
            'localize.default_locale.string' => __p('localize::admin.please_choose_default_locale'),
            'localize.default_locale.exists' => __p('localize::admin.please_choose_default_locale'),
        ]);

        return $validator->validated();
    }
}
