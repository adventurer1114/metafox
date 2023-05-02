<?php

namespace MetaFox\Localize\Http\Resources\v1\Language\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Localize\Models\Language;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * GenerateNewAppForm
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class MakeLanguageForm.
 * @ignore
 * @codeCoverageIgnore
 */
class MakeLanguageForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this
            ->title(__p('core::phrase.create_language_pack'))
            ->action('admincp/localize/language')
            ->asPost()
            ->setValue([
                '--direction'     => 'ltr',
                '--base_language' => config('app.locale'),
                '--language_code' => '',
                '--vendor'        => 'phpFox',
                '--title'         => '',
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::text('--title')
                ->required()
                ->label(__p('localize::phrase.language_name'))
                ->description(__p('localize::phrase.language_name_desc')) //'etc: English, Abkhazian, Arabic, ...'
                ->maxLength(64)
                ->yup(Yup::string()->required()),
            Builder::text('--language_code')
                ->required()
                ->disabled($this->isEdit())
                ->label(__p('app::phrase.language_code'))
                ->description(__p('localize::phrase.language_code_desc'))
                ->yup(
                    Yup::string()
                        ->maxLength(2)
                        ->minLength(2)
                        ->required()
                        ->matches('^([a-z]){2}$', __p('localize::validation.invalid_format'))
                ),
            Builder::text('--vendor')
                ->required()
                ->disabled($this->isEdit())
                ->label(__p('core::phrase.vendor_name'))
                ->maxLength(64)
                ->description(__p('core::phrase.vendor_name_desc'))
                ->yup(
                    $this->isEdit()
                        ? Yup::string()
                        : Yup::string()
                        ->required()
                        ->matches('^(\w+)$', __p('localize::validation.invalid_format'))
                ),
//  @link https://meta.wikimedia.org/wiki/Template:List_of_language_names_ordered_by_code
//            Builder::choice('--direction') // temporary disable direction, use main locale.
//                ->required()
//                ->label(__p('localize::phrase.direction'))
//                ->description(__p('localize::phrase.language_direction'))
//                ->options([
//                    ['value' => 'ltr', 'label' => __p('localize::phrase.left_to_right')],
//                    ['value' => 'rtl', 'label' => __p('localize::phrase.right_to_left')],
//                ]),
        );
        if (!$this->isEdit()) {
            $basic->addFields(
                Builder::choice('--base_language')
                    ->required()
                    ->label(__p('localize::phrase.root_language'))
                    ->options($this->getLanguageOptions()),
            );
        }
        $this->addDefaultFooter();
    }

    private function getLanguageOptions()
    {
        $rows     = Language::query()->pluck('name', 'language_code')->toArray();
        $response = [];
        foreach ($rows as $value => $label) {
            $response[] = ['value' => $value, 'label' => $label];
        }

        return $response;
    }

    protected function isEdit(): bool
    {
        return $this->resource?->entityId() ? true : false;
    }
}
