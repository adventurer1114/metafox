<?php

namespace MetaFox\Localize\Http\Resources\v1\Phrase\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Localize\Models\Phrase as Model;
use MetaFox\Localize\Repositories\PhraseRepositoryInterface;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class StorePhraseForm.
 * @property Model $resource
 */
class StorePhraseForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->asPost()
            ->title(__p('core::phrase.add_new_phrase'))
            ->action('admincp/phrase')
            ->setValue([
                'package_id' => 'core',
                'group'      => 'phrase',
                'locale'     => config('app.locale'),
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::choice('locale')
                ->required()
                ->label(__p('localize::phrase.language'))
                ->options([['label' => 'English', 'value' => 'en']])
                ->yup(Yup::string()->required()),
            Builder::selectPackage('package_id')
                ->label(__p('core::phrase.package_name'))
                ->required(),
            Builder::choice('group')
                ->options(resolve(PhraseRepositoryInterface::class)->getGroupOptions())
                ->freeSolo()
                ->label(__p('localize::phrase.group'))
                ->required()
                ->yup(Yup::string()->required()->maxLength(32)),
            Builder::text('name')
                ->label(__p('localize::phrase.phrase_name'))
                ->required()
                ->yup(Yup::string()->required()->maxLength(64)),
            Builder::textArea('text')
                ->label(__p('localize::phrase.text_value')),
        );

        $this->addDefaultFooter();
    }
}
