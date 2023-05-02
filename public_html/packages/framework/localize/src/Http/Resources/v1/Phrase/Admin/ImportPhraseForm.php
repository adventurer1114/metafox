<?php

namespace MetaFox\Localize\Http\Resources\v1\Phrase\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;
use MetaFox\Localize\Models\Phrase as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class ImportPhraseForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class ImportPhraseForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('core::phrase.edit'))
            ->action(apiUrl('admin.localize.phrase.import'))
            ->asMultipart();
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::rawFile('file')
                    ->required()
                    ->placeholder(__p('localize::phrase.upload_phrase_csv_file'))
                    ->accept('text/csv'),
            );

        $this->addFooter()
            ->addFields(
                Builder::submit()
                    ->label(__p('core::web.upload')),
                Builder::cancelButton(),
            );
    }
}
