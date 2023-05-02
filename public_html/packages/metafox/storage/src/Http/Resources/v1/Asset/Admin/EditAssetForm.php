<?php

namespace MetaFox\Storage\Http\Resources\v1\Asset\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;
use MetaFox\Storage\Models\Asset as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class EditAssetForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class EditAssetForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('core::phrase.upload'))
            ->action(apiUrl('admin.storage.asset.upload', ['asset' => $this->resource->id]))
            ->asPost()
            ->asMultipart()
            ->setValue([
            ]);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::rawFile('file')
                    ->required()
                    ->placeholder(__p('core::phrase.upload'))
                    ->label(__p('core::phrase.title')),
            );

        $this->addDefaultFooter(true);
    }
}
